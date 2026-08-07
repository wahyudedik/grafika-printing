<?php

namespace App\Traits;

use App\Models\Vendor as VendorModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait untuk mengurangi duplikasi kode vendor context retrieval.
 *
 * Digunakan oleh controller yang membutuhkan vendor context.
 * Menggantikan pola: Auth::user()->vendorUser->first() yang diulang 49x.
 *
 * @example
 *   class ProdukController extends Controller
 *   {
 *       use HasVendorContext;
 *
 *       public function index()
 *       {
 *           $vendor = $this->getVendor();
 *           // ...
 *       }
 *   }
 */
trait HasVendorContext
{
    /**
     * Get the current authenticated user's vendor.
     *
     * @return Vendor|null
     */
    protected function getVendor(): ?VendorModel
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return $user->vendorUser->first();
    }

    /**
     * Get the current authenticated user's vendor ID.
     *
     * @return int|null
     */
    protected function getVendorId(): ?int
    {
        $vendor = $this->getVendor();
        return $vendor ? $vendor->id : null;
    }

    /**
     * Ensure the current user has a vendor context.
     * Throws exception if no vendor found.
     *
     * @return Vendor
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function requireVendor(): VendorModel
    {
        $vendor = $this->getVendor();
        if (!$vendor) {
            abort(403, 'Tidak ada akun vendor yang terkait dengan user ini.');
        }
        return $vendor;
    }
}
