<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'option',
        'value'
    ];

    /**
     * Default Company Settings
     *
     * @var array
     */
    public static $defaultSettings = [
        'language' => 'en',
        'date_format' => 'Y-m-d',
        'timezone' => 'Africa/Johannesburg',
        'currency_id' => 4,
        'financial_month_starts' => '3',
        'financial_month_ends' => '2',
        'invoice_prefix' => 'INV',
        'estimate_prefix' => 'QUO',
        'payment_prefix' => 'PAY',
        'tax_per_item' => false,
        'display_subaccount' => true,
        'discount_per_item' => false,
        'invoice_color' => '#308AF3',
        'invoice_auto_archive' => false,
        'invoice_footer' => '',
        'estimate_color' => '#308AF3',
        'estimate_footer' => '',
        'estimate_auto_archive' => false,
        'order_color' => '#308AF3',
        'display_selling_prices' => false,
        'display_cost_prices' => false,
        'payment_color' => '#308AF3',
        'payment_footer' => '',
        'payment_auto_archive' => false,
        'invoice_mail_subject' => 'Invoice {invoice.number} from {company.name}',
        'invoice_mail_content' => '<p>Dear {customer.display_name},</p><p><br></p><p>Please find the attached invoice from the link below. We appreciate your prompt payment.</p><p><br></p><p>{invoice.link}</p><p><br></p><p>If you have any question, feel free to contact us. </p><p><br></p><p>Thank you,</p><p>{company.name}.</p>',
        'estimate_mail_subject' => 'Estimate {estimate.number} from {company.name}',
        'estimate_mail_content' => '<p>Dear {customer.display_name},</p><p><br></p><p>Please find the attached estimate from the link below.</p><p><br></p><p>{estimate.link}</p><p><br></p><p>If you have any question, feel free to contact us. </p><p><br></p><p>Thank you,</p><p>{company.name}.</p>',
        'order_mail_subject' => 'Order {order.number} from {company.name}',
        'order_mail_content' => '<p>Dear {customer.display_name},</p><p><br></p><p>Please find the attached estimate from the link below.</p><p><br></p><p>{estimate.link}</p><p><br></p><p>If you have any question, feel free to contact us. </p><p><br></p><p>Thank you,</p><p>{company.name}.</p>',
        'order_customer_confirmation' => false,
        'order_fulfillment_notification' => false,
        'fulfillment_mailbox' => 'fulfillment@example.com',
        'payment_mail_subject' => 'Payment Receipt {payment.number} from {company.name}',
        'payment_mail_content' => '<p>Dear {customer.display_name},</p><p><br></p><p>Thank you for the payment. </p><p>Please find the attached payment receipt from the link below.</p><p><br></p><p>{payment.link}</p><p><br></p><p>If you have any question, feel free to contact us. </p><p><br></p><p>Thank you,</p><p>{company.name}.</p>',
        'paypal_username' => '',
        'paypal_password' => '',
        'paypal_signature' => '',
        'paypal_test_mode' => false,
        'paypal_active' => false,
        'stripe_public_key' => '',
        'stripe_secret_key' => '',
        'stripe_test_mode' => false,
        'stripe_active' => false,
        'razorpay_id' => '',
        'razorpay_secret_key' => '',
        'razorpay_test_mode' => false,
        'razorpay_active' => false,
        'avatar' => null,
        'invoice_template' => 'template_1',
        'estimate_template' => 'template_1',
        'payment_template' => 'template_1',
        'mollie_api_key' => '',
        'mollie_test_mode' => false,
        'mollie_active' => false,
        'estimate_auto_convert' => false,
        'invoice_from_template' => '<p class="mb-0"><strong>{company.name}</strong></p><p class="mb-0">{company.billing.address_1}</p><p class="mb-0">{company.billing.address_2}</p><p class="mb-0">{company.billing.city}, {company.billing.state}</p><p class="mb-0">{company.billing.country}</p><p class="mb-0">{company.billing.phone}</p><p class="mb-0">VAT: {company.vat_number}</p>',
        'invoice_to_template' => '<p class="mb-0"><strong>{customer.name}</strong></p><p class="mb-0">{customer.billing.address_1}</p><p class="mb-0">{customer.billing.address_2}</p><p class="mb-0">{customer.billing.city}, {customer.billing.state}</p><p class="mb-0">{customer.billing.country}</p><p class="mb-0">{customer.billing.phone}</p><p class="mb-0">VAT: {customer.vat_number}</p>',
        'invoice_ships_to_template' => '<p class="mb-0">{customer.shipping.address_1}</p><p class="mb-0">{customer.shipping.address_2}</p><p class="mb-0">{customer.shipping.city}, {customer.shipping.state}</p><p class="mb-0">{customer.shipping.country}</p><p class="mb-0">{customer.shipping.phone}</p>',
        'estimate_from_template' => '<p class="mb-0"><strong>{company.name}</strong></p><p class="mb-0">{company.billing.address_1}</p><p class="mb-0">{company.billing.address_2}</p><p class="mb-0">{company.billing.city}, {company.billing.state}</p><p class="mb-0">{company.billing.country}</p><p class="mb-0">{company.billing.phone}</p><p class="mb-0">VAT: {company.vat_number}</p>',
        'estimate_to_template' => '<p class="mb-0"><strong>{customer.name}</strong></p><p class="mb-0">{customer.billing.address_1}</p><p class="mb-0">{customer.billing.address_2}</p><p class="mb-0">{customer.billing.city}, {customer.billing.state}</p><p class="mb-0">{customer.billing.country}</p><p class="mb-0">{customer.billing.phone}</p><p class="mb-0">VAT: {customer.vat_number}</p>',
        'estimate_ships_to_template' => '<p class="mb-0">{customer.shipping.address_1}</p><p class="mb-0">{customer.shipping.address_2}</p><p class="mb-0">{customer.shipping.city}, {customer.shipping.state}</p><p class="mb-0">{customer.shipping.country}</p><p class="mb-0">{customer.shipping.phone}</p>',
        'payment_from_template' => '<p class="mb-0"><strong>{company.name}</strong></p><p class="mb-0">{company.billing.address_1}</p><p class="mb-0">{company.billing.address_2}</p><p class="mb-0">{company.billing.city}, {company.billing.state}</p><p class="mb-0">{company.billing.country}</p><p class="mb-0">{company.billing.phone}</p><p class="mb-0">VAT: {company.vat_number}</p>',
        'payment_to_template' => '<p class="mb-0"><strong>{customer.name}</strong></p><p class="mb-0">{customer.billing.address_1}</p><p class="mb-0">{customer.billing.address_2}</p><p class="mb-0">{customer.billing.city}, {customer.billing.state}</p><p class="mb-0">{customer.billing.country}</p><p class="mb-0">{customer.billing.phone}</p><p class="mb-0">VAT: {customer.vat_number}</p>',
        'payment_ships_to_template' => '<p class="mb-0">{customer.shipping.address_1}</p><p class="mb-0">{customer.shipping.address_2}</p><p class="mb-0">{customer.shipping.city}, {customer.shipping.state}</p><p class="mb-0">{customer.shipping.country}</p><p class="mb-0">{customer.shipping.phone}</p>',
        'invoice_show_payments_on_pdf' => true,
        'shop_announcement' => '',
        'sales_locations' => true,

        // E-commerce Features
        'b2b_ecommerce_enabled' => true,
        'ecommerce_guest_checkout' => false,
        'ecommerce_public_prices' => true,
        'ecommerce_backorders' => false,
        'ecommerce_require_approval' => true,
        'ecommerce_show_stock' => true,
        'ecommerce_allow_partial_delivery' => false,
        'ecommerce_min_order_amount' => 0,
        'ecommerce_order_confirmation_email' => true,
        'ecommerce_fulfillment_notification' => false,

        // E-commerce Display Settings
        'ecommerce_products_per_page' => 24,
        'ecommerce_currency_display' => 'before', // before or after amount
        'ecommerce_show_tax_inclusive' => true,
        'ecommerce_allow_price_override' => false,
        'ecommerce_show_product_images' => true,

        // E-commerce Customer Settings
        'ecommerce_new_customer_requires_approval' => true,
        'ecommerce_customer_can_view_orders' => true,
        'ecommerce_customer_can_download_invoices' => true,
        'ecommerce_customer_can_view_statements' => false,

        // E-commerce Shipping
        'ecommerce_shipping_enabled' => false,
        'ecommerce_shipping_calculation' => 'flat', // flat, weight, zone
        'ecommerce_flat_shipping_rate' => 0,
        'ecommerce_free_shipping_threshold' => 0,

        // E-commerce Payment
        'ecommerce_payment_terms' => 'account', // account, cod, prepaid
        'ecommerce_allow_credit_card' => false,
        'ecommerce_allow_eft' => true,

        // E-commerce Delivery
        'ecommerce_delivery_enabled' => false,
    ];

    /**
     * Define Relation with User Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Set new or update existing Company Settings.
     *
     * @param string $key
     * @param string $setting
     * @param string $company_id
     *
     * @return void
     */
    public static function setSetting($key, $setting, $company_id): void
    {
        $old = self::whereOption($key)->findByCompany($company_id)->first();

        if ($old) {
            $old->value = $setting;
            $old->save();
            return;
        }

        $set = new CompanySetting();
        $set->option = $key;
        $set->value = $setting;
        $set->company_id = $company_id;
        $set->save();
    }

    /**
     * Get Default Company Setting.
     *
     * @param string $key
     *
     * @return string|null
     */
    public static function getDefaultSetting($key)
    {
        $setting = self::$defaultSettings[$key];

        if ($setting) {
            return $setting;
        } else {
            return null;
        }
    }

    /**
     * Get Company Setting.
     *
     * @param string $key
     * @param string $company_id
     *
     * @return string|null
     */
    public static function getSetting($key, $company_id)
    {
        $setting = static::whereOption($key)->findByCompany($company_id)->first();

        if ($setting) {
            return $setting->value;
        } else {
            return self::getDefaultSetting($key);
        }
    }

    /**
     * Scope a query to only include settings of a given company.
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param int $company_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindByCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }
}
