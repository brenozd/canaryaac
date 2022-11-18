<?php
/**
 * Payments Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use App\Model\Entity\Payments as EntityPayments;
use App\Model\Entity\PaymentStatus;
use App\Model\Entity\ServerConfig as EntityServerConfig;

class Payments
{

    public static function getProducts()
    {
        $select_ServerConfig = EntityServerConfig::getInfoWebsite()->fetchObject();
        $select_products = EntityServerConfig::getProducts(null, 'id ASC');
        while ($product = $select_products->fetchObject()) {
            $final_price = $select_ServerConfig->coin_price * $product->coins;
            $arrayProducts[] = [
                'id' => $product->id,
                'coins' => $product->coins,
                'final_price' => $final_price
            ];
        }
        return $arrayProducts;
    }

    public static function getPayments()
    {
        $select_payments = EntityPayments::getPayment(null, 'id DESC');
        while ($payment = $select_payments->fetchObject()) {
            $status=PaymentStatus::from($payment->status);
            $arrayPayments[] = [
                'id' => $payment->id,
                'account_id' => $payment->account_id,
                'method' => $payment->method,
                'method_img' => self::convertMethodImage($payment->method),
                'reference' => $payment->reference,
                'total_coins' => $payment->total_coins,
                'final_price' => $payment->final_price,
                'status' => $status,
                'status_badge' => self::convertStatus($status),
                'date' => date('d/m/Y h:i:s', $payment->date),
            ];
        }
        return $arrayPayments ?? '';
    }

    public static function getPaymentBetweenDate($month = '1')
    {
        $current_year = date('Y');
        $date_start = strtotime($current_year . '-' . $month . '-01');
        $date_end = strtotime($current_year . '-' . $month . '-' . date('t'));

        $payment_canceled_coins = 0;
        $payment_paid_coins = 0;

        $payment_paid_price = 0;
        $payment_canceled_price = 0;

        $select_payments = EntityPayments::getPayment('date BETWEEN "' . $date_start . '" AND "' . $date_end . '"');
        while ($payment = $select_payments->fetchObject()) {
            if ($payment->status == PaymentStatus::Approved) {
                $payment_paid_coins += $payment->total_coins;
                $payment_paid_price += $payment->final_price;
            }
            if ($payment->status == PaymentStatus::Canceled || $payment->status == PaymentStatus::Rejected) {
                $payment_canceled_coins += $payment->total_coins;
                $payment_canceled_price += $payment->final_price;
            }
        }
        return [
            'coins' => [
                'paid' => $payment_paid_coins,
                'canceled' => $payment_canceled_coins
            ],
            'price' => [
                'paid' => $payment_paid_price,
                'canceled' => $payment_canceled_price
            ]
        ];
    }

    public static function statisticsPaytments()
    {
        $total_coins_approved = 0;
        $final_price_approved = 0;
        $select_payments_approved = EntityPayments::getPayment('status = '.PaymentStatus::Approved->value);
        while ($payment_approved = $select_payments_approved->fetchObject()) {
            $total_coins_approved += $payment_approved->total_coins;
            $final_price_approved += $payment_approved->final_price;
        }

        $total_coins_cancelled = 0;
        $final_price_cancelled = 0;
        $select_payments_cancelled = EntityPayments::getPayment('status = '.PaymentStatus::Canceled->value);
        while ($payment_cancelled = $select_payments_cancelled->fetchObject()) {
            $total_coins_cancelled += $payment_cancelled->total_coins;
            $final_price_cancelled += $payment_cancelled->final_price;
        }

        $arrayStats = [
            'total_coins' => $total_coins_approved,
            'final_price' => $final_price_approved,
            'cancel_coins' => $total_coins_cancelled,
            'cancel_price' => $final_price_cancelled,
        ];
        return $arrayStats;
    }

    public static function convertStatus($status)
    {
        switch ($status) {
            case PaymentStatus::Pending:
                return '<span class="badge rounded-pill badge-light-warning" text-capitalized=""> Pending </span>';
                break;
            case PaymentStatus::UnderAnalisys:
                return '<span class="badge rounded-pill badge-light-warning" text-capitalized=""> Under Analysis </span>';
                break;
            case PaymentStatus::Processing:
                return '<span class="badge rounded-pill badge-light-info" text-capitalized=""> Processing </span>';
                break;
            case PaymentStatus::Approved:
                return '<span class="badge rounded-pill badge-light-success" text-capitalized=""> Approved </span>';
                break;
            case PaymentStatus::Rejected:
                return '<span class="badge rounded-pill badge-light-danger" text-capitalized=""> Rejected </span>';
                break;
            case PaymentStatus::Canceled:
                return '<span class="badge rounded-pill badge-light-danger" text-capitalized=""> Canceled </span>';
                break;
            default:
                return '<span class="badge rounded-pill badge-light-danger" text-capitalized=""> Unkown </span>';
                break;
        }
    }

    public static function convertMethodImage($method)
    {
        switch ($method) {
            case 'paypal':
                return '<img src="'. URL . '/resources/images/payment/paymentmethodcategory31.gif">';
                exit;
            case 'pagseguro':
                return '<img src="'. URL . '/resources/images/payment/paymentmethodcategory32.gif">';
                exit;
            case 'mercadopago':
                return '<img src="'. URL . '/resources/images/payment/paymentmethodcategory144.gif">';
                exit;
            default:
                return '';
                exit;
        }
    }

}