<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_usertype_email_index')) {
                $table->index(['usertype', 'email'], 'users_usertype_email_index');
            }
            if (!$this->indexExists('users', 'users_created_at_index')) {
                $table->index(['created_at'], 'users_created_at_index');
            }
            if (!$this->indexExists('users', 'users_email_verified_at_index')) {
                $table->index(['email_verified_at'], 'users_email_verified_at_index');
            }
        });

        // Auctions table indexes
        Schema::table('auctions', function (Blueprint $table) {
            if (!$this->indexExists('auctions', 'auctions_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'auctions_status_created_at_index');
            }
            if (!$this->indexExists('auctions', 'auctions_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'auctions_user_id_status_index');
            }
            if (!$this->indexExists('auctions', 'auctions_deadline_index')) {
                $table->index(['deadline'], 'auctions_deadline_index');
            }
            if (!$this->indexExists('auctions', 'auctions_admin_approval_status_index')) {
                $table->index(['admin_approval_status'], 'auctions_admin_approval_status_index');
            }
            if (!$this->indexExists('auctions', 'auctions_status_admin_approval_created_index')) {
                $table->index(['status', 'admin_approval_status', 'created_at'], 'auctions_status_admin_approval_created_index');
            }
        });

        // Auction bids table indexes
        Schema::table('auction_bids', function (Blueprint $table) {
            if (!$this->indexExists('auction_bids', 'auction_bids_auction_id_vendor_id_index')) {
                $table->index(['auction_id', 'vendor_id'], 'auction_bids_auction_id_vendor_id_index');
            }
            if (!$this->indexExists('auction_bids', 'auction_bids_created_at_index')) {
                $table->index(['created_at'], 'auction_bids_created_at_index');
            }
            if (!$this->indexExists('auction_bids', 'auction_bids_vendor_id_index')) {
                $table->index(['vendor_id'], 'auction_bids_vendor_id_index');
            }
            if (!$this->indexExists('auction_bids', 'auction_bids_auction_id_index')) {
                $table->index(['auction_id'], 'auction_bids_auction_id_index');
            }
        });

        // Xendit payments table indexes
        Schema::table('xendit_payments', function (Blueprint $table) {
            if (!$this->indexExists('xendit_payments', 'xendit_payments_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'xendit_payments_user_id_status_index');
            }
            if (!$this->indexExists('xendit_payments', 'xendit_payments_auction_id_status_index')) {
                $table->index(['auction_id', 'status'], 'xendit_payments_auction_id_status_index');
            }
            if (!$this->indexExists('xendit_payments', 'xendit_payments_external_id_index')) {
                $table->index(['external_id'], 'xendit_payments_external_id_index');
            }
            if (!$this->indexExists('xendit_payments', 'xendit_payments_created_at_index')) {
                $table->index(['created_at'], 'xendit_payments_created_at_index');
            }
            if (!$this->indexExists('xendit_payments', 'xendit_payments_user_status_created_index')) {
                $table->index(['user_id', 'status', 'created_at'], 'xendit_payments_user_status_created_index');
            }
        });

        // Vendor wallets table indexes
        Schema::table('vendor_wallets', function (Blueprint $table) {
            if (!$this->indexExists('vendor_wallets', 'vendor_wallets_vendor_id_index')) {
                $table->index(['vendor_id'], 'vendor_wallets_vendor_id_index');
            }
            if (!$this->indexExists('vendor_wallets', 'vendor_wallets_is_active_index')) {
                $table->index(['is_active'], 'vendor_wallets_is_active_index');
            }
        });

        // Vendor withdrawals table indexes
        Schema::table('vendor_withdrawals', function (Blueprint $table) {
            if (!$this->indexExists('vendor_withdrawals', 'vendor_withdrawals_vendor_id_status_index')) {
                $table->index(['vendor_id', 'status'], 'vendor_withdrawals_vendor_id_status_index');
            }
            if (!$this->indexExists('vendor_withdrawals', 'vendor_withdrawals_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'vendor_withdrawals_status_created_at_index');
            }
            if (!$this->indexExists('vendor_withdrawals', 'vendor_withdrawals_withdrawal_code_index')) {
                $table->index(['withdrawal_code'], 'vendor_withdrawals_withdrawal_code_index');
            }
        });

        // Order trackings table indexes
        Schema::table('order_trackings', function (Blueprint $table) {
            if (!$this->indexExists('order_trackings', 'order_trackings_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'order_trackings_user_id_status_index');
            }
            if (!$this->indexExists('order_trackings', 'order_trackings_vendor_id_status_index')) {
                $table->index(['vendor_id', 'status'], 'order_trackings_vendor_id_status_index');
            }
            if (!$this->indexExists('order_trackings', 'order_trackings_auction_id_index')) {
                $table->index(['auction_id'], 'order_trackings_auction_id_index');
            }
            if (!$this->indexExists('order_trackings', 'order_trackings_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'order_trackings_status_created_at_index');
            }
        });

        // Mediation requests table indexes
        Schema::table('mediation_requests', function (Blueprint $table) {
            if (!$this->indexExists('mediation_requests', 'mediation_requests_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'mediation_requests_status_created_at_index');
            }
            if (!$this->indexExists('mediation_requests', 'mediation_requests_auction_id_index')) {
                $table->index(['auction_id'], 'mediation_requests_auction_id_index');
            }
            if (!$this->indexExists('mediation_requests', 'mediation_requests_requested_by_index')) {
                $table->index(['requested_by'], 'mediation_requests_requested_by_index');
            }
        });

        // Admin fee transactions table indexes
        Schema::table('admin_fee_transactions', function (Blueprint $table) {
            if (!$this->indexExists('admin_fee_transactions', 'admin_fee_transactions_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'admin_fee_transactions_user_id_status_index');
            }
            if (!$this->indexExists('admin_fee_transactions', 'admin_fee_transactions_vendor_id_status_index')) {
                $table->index(['vendor_id', 'status'], 'admin_fee_transactions_vendor_id_status_index');
            }
            if (!$this->indexExists('admin_fee_transactions', 'admin_fee_transactions_auction_id_index')) {
                $table->index(['auction_id'], 'admin_fee_transactions_auction_id_index');
            }
        });

        // Vendor ratings table indexes
        Schema::table('vendor_ratings', function (Blueprint $table) {
            if (!$this->indexExists('vendor_ratings', 'vendor_ratings_vendor_id_index')) {
                $table->index(['vendor_id'], 'vendor_ratings_vendor_id_index');
            }
            if (!$this->indexExists('vendor_ratings', 'vendor_ratings_user_id_index')) {
                $table->index(['user_id'], 'vendor_ratings_user_id_index');
            }
            if (!$this->indexExists('vendor_ratings', 'vendor_ratings_auction_id_index')) {
                $table->index(['auction_id'], 'vendor_ratings_auction_id_index');
            }
        });

        // Delivery confirmations table indexes
        Schema::table('delivery_confirmations', function (Blueprint $table) {
            if (!$this->indexExists('delivery_confirmations', 'delivery_confirmations_user_id_index')) {
                $table->index(['user_id'], 'delivery_confirmations_user_id_index');
            }
            if (!$this->indexExists('delivery_confirmations', 'delivery_confirmations_vendor_id_index')) {
                $table->index(['vendor_id'], 'delivery_confirmations_vendor_id_index');
            }
            if (!$this->indexExists('delivery_confirmations', 'delivery_confirmations_auction_id_index')) {
                $table->index(['auction_id'], 'delivery_confirmations_auction_id_index');
            }
        });

        // Shipping invoices table indexes
        Schema::table('shipping_invoices', function (Blueprint $table) {
            if (!$this->indexExists('shipping_invoices', 'shipping_invoices_user_id_index')) {
                $table->index(['user_id'], 'shipping_invoices_user_id_index');
            }
            if (!$this->indexExists('shipping_invoices', 'shipping_invoices_vendor_id_index')) {
                $table->index(['vendor_id'], 'shipping_invoices_vendor_id_index');
            }
            if (!$this->indexExists('shipping_invoices', 'shipping_invoices_auction_id_index')) {
                $table->index(['auction_id'], 'shipping_invoices_auction_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        Schema::table('shipping_invoices', function (Blueprint $table) {
            $table->dropIndex(['auction_id']);
            $table->dropIndex(['vendor_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('delivery_confirmations', function (Blueprint $table) {
            $table->dropIndex(['auction_id']);
            $table->dropIndex(['vendor_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('vendor_ratings', function (Blueprint $table) {
            $table->dropIndex(['auction_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['vendor_id']);
        });

        Schema::table('admin_fee_transactions', function (Blueprint $table) {
            $table->dropIndex(['auction_id']);
            $table->dropIndex(['vendor_id', 'status']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('mediation_requests', function (Blueprint $table) {
            $table->dropIndex(['requested_by']);
            $table->dropIndex(['auction_id']);
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('order_trackings', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['auction_id']);
            $table->dropIndex(['vendor_id', 'status']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('vendor_withdrawals', function (Blueprint $table) {
            $table->dropIndex(['withdrawal_code']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['vendor_id', 'status']);
        });

        Schema::table('vendor_wallets', function (Blueprint $table) {
            $table->dropIndex(['is_frozen']);
            $table->dropIndex(['vendor_id']);
        });

        Schema::table('xendit_payments', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status', 'created_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['external_id']);
            $table->dropIndex(['auction_id', 'status']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('auction_bids', function (Blueprint $table) {
            $table->dropIndex(['auction_id', 'vendor_id', 'is_winning']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_winning', 'auction_id']);
            $table->dropIndex(['auction_id', 'vendor_id']);
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->dropIndex(['status', 'admin_approval_status', 'created_at']);
            $table->dropIndex(['admin_approval_status']);
            $table->dropIndex(['deadline']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email_verified_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['usertype', 'email']);
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$index}'");
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};
