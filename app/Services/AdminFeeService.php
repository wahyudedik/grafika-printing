<?php

namespace App\Services;

use App\Models\AdminFeeSetting;
use App\Models\AdminFeeTransaction;
use App\Models\Auction;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminFeeService
{
    /**
     * Calculate admin fees for auction
     */
    public function calculateAdminFees($auctionAmount, $category = 'auction')
    {
        $settings = AdminFeeSetting::getActiveSettings($category);
        $totalFee = 0;
        $feeBreakdown = [];

        foreach ($settings as $setting) {
            if ($setting->isEffective()) {
                $fee = $setting->calculateFee($auctionAmount);
                if ($fee > 0) {
                    $totalFee += $fee;
                    $feeBreakdown[] = $setting->getFeeBreakdown($auctionAmount);
                }
            }
        }

        return [
            'total_fee' => $totalFee,
            'fee_breakdown' => $feeBreakdown,
            'settings_applied' => count($feeBreakdown)
        ];
    }

    /**
     * Calculate payment gateway fees
     */
    public function calculatePaymentGatewayFees($amount, $paymentMethod = 'xendit')
    {
        // Xendit fees (example rates)
        $xenditFees = [
            'credit_card' => 0.029, // 2.9%
            'bank_transfer' => 0.015, // 1.5%
            'ewallet' => 0.02, // 2%
            'retail_outlet' => 0.01, // 1%
        ];

        $feeRate = $xenditFees[$paymentMethod] ?? 0.02; // Default 2%
        $fee = $amount * $feeRate;

        return [
            'fee_rate' => $feeRate,
            'fee_amount' => $fee,
            'payment_method' => $paymentMethod
        ];
    }

    /**
     * Calculate total fees for auction
     */
    public function calculateTotalFees($auctionAmount, $paymentMethod = 'bank_transfer')
    {
        $adminFees = $this->calculateAdminFees($auctionAmount);
        $paymentGatewayFees = $this->calculatePaymentGatewayFees($auctionAmount, $paymentMethod);

        $totalAdminFee = $adminFees['total_fee'];
        $totalPaymentFee = $paymentGatewayFees['fee_amount'];
        $totalFees = $totalAdminFee + $totalPaymentFee;
        $totalAmount = $auctionAmount + $totalFees;

        return [
            'auction_amount' => $auctionAmount,
            'admin_fee' => $totalAdminFee,
            'payment_gateway_fee' => $totalPaymentFee,
            'total_fees' => $totalFees,
            'total_amount' => $totalAmount,
            'vendor_receives' => $auctionAmount,
            'admin_receives' => $totalFees,
            'admin_fee_breakdown' => $adminFees['fee_breakdown'],
            'payment_gateway_breakdown' => $paymentGatewayFees
        ];
    }

    /**
     * Create admin fee transaction
     */
    public function createTransaction($auctionId, $vendorId, $userId, $auctionAmount, $paymentMethod = 'bank_transfer')
    {
        $fees = $this->calculateTotalFees($auctionAmount, $paymentMethod);

        $transaction = AdminFeeTransaction::createTransaction(
            $auctionId,
            $vendorId,
            $userId,
            $auctionAmount,
            $fees['admin_fee'],
            $fees['payment_gateway_fee'],
            $fees['admin_fee_breakdown']
        );

        Log::info('Admin fee transaction created', [
            'transaction_id' => $transaction->id,
            'auction_id' => $auctionId,
            'vendor_id' => $vendorId,
            'user_id' => $userId,
            'auction_amount' => $auctionAmount,
            'admin_fee' => $fees['admin_fee'],
            'payment_gateway_fee' => $fees['payment_gateway_fee'],
            'total_amount' => $fees['total_amount']
        ]);

        return $transaction;
    }

    /**
     * Get fee preview for auction
     */
    public function getFeePreview($auctionAmount, $paymentMethod = 'bank_transfer')
    {
        $fees = $this->calculateTotalFees($auctionAmount, $paymentMethod);

        return [
            'auction_amount' => $auctionAmount,
            'admin_fee' => $fees['admin_fee'],
            'payment_gateway_fee' => $fees['payment_gateway_fee'],
            'total_fees' => $fees['total_fees'],
            'total_amount' => $fees['total_amount'],
            'vendor_receives' => $fees['vendor_receives'],
            'admin_receives' => $fees['admin_receives'],
            'fee_percentage' => $auctionAmount > 0 ?
                round(($fees['admin_fee'] / $auctionAmount) * 100, 2) : 0,
            'breakdown' => [
                'admin_fees' => $fees['admin_fee_breakdown'],
                'payment_gateway' => $fees['payment_gateway_breakdown']
            ]
        ];
    }

    /**
     * Update auction with admin fees
     */
    public function updateAuctionWithFees(Auction $auction, $paymentMethod = 'bank_transfer')
    {
        $fees = $this->calculateTotalFees($auction->budget, $paymentMethod);

        $auction->update([
            'admin_fee_amount' => $fees['admin_fee'],
            'payment_gateway_fee' => $fees['payment_gateway_fee'],
            'total_amount_with_fees' => $fees['total_amount'],
            'vendor_receives' => $fees['vendor_receives'],
            'admin_receives' => $fees['admin_receives']
        ]);

        return $fees;
    }

    /**
     * Update vendor bid with admin fees
     */
    public function updateBidWithFees($bidAmount, $paymentMethod = 'bank_transfer')
    {
        $fees = $this->calculateTotalFees($bidAmount, $paymentMethod);

        return [
            'bid_amount' => $bidAmount,
            'admin_fee' => $fees['admin_fee'],
            'payment_gateway_fee' => $fees['payment_gateway_fee'],
            'total_amount' => $fees['total_amount'],
            'vendor_receives' => $fees['vendor_receives'],
            'admin_receives' => $fees['admin_receives']
        ];
    }

    /**
     * Get admin fee statistics
     */
    public function getAdminFeeStatistics($startDate = null, $endDate = null)
    {
        $query = AdminFeeTransaction::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $transactions = $query->get();

        $totalTransactions = $transactions->count();
        $totalAuctionAmount = $transactions->sum('auction_amount');
        $totalAdminFees = $transactions->sum('admin_fee_amount');
        $totalPaymentFees = $transactions->sum('payment_gateway_fee');
        $totalAdminReceives = $transactions->sum('admin_receives');
        $totalVendorReceives = $transactions->sum('vendor_receives');

        return [
            'total_transactions' => $totalTransactions,
            'total_auction_amount' => $totalAuctionAmount,
            'total_admin_fees' => $totalAdminFees,
            'total_payment_fees' => $totalPaymentFees,
            'total_admin_receives' => $totalAdminReceives,
            'total_vendor_receives' => $totalVendorReceives,
            'average_admin_fee_percentage' => $totalAuctionAmount > 0 ?
                round(($totalAdminFees / $totalAuctionAmount) * 100, 2) : 0,
            'average_transaction_amount' => $totalTransactions > 0 ?
                round($totalAuctionAmount / $totalTransactions, 2) : 0
        ];
    }

    /**
     * Get vendor fee statistics
     */
    public function getVendorFeeStatistics($vendorId, $startDate = null, $endDate = null)
    {
        $query = AdminFeeTransaction::forVendor($vendorId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $transactions = $query->get();

        $totalTransactions = $transactions->count();
        $totalAuctionAmount = $transactions->sum('auction_amount');
        $totalAdminFees = $transactions->sum('admin_fee_amount');
        $totalVendorReceives = $transactions->sum('vendor_receives');

        return [
            'vendor_id' => $vendorId,
            'total_transactions' => $totalTransactions,
            'total_auction_amount' => $totalAuctionAmount,
            'total_admin_fees' => $totalAdminFees,
            'total_vendor_receives' => $totalVendorReceives,
            'average_admin_fee_percentage' => $totalAuctionAmount > 0 ?
                round(($totalAdminFees / $totalAuctionAmount) * 100, 2) : 0
        ];
    }
}
