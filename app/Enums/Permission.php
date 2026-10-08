<?php

declare(strict_types=1);

namespace App\Enums;

enum Permission: string
{
    case QuotationsView = 'quotations.view';
    case QuotationsCreate = 'quotations.create';
    case QuotationsUpdate = 'quotations.update';
    case QuotationsApprove = 'quotations.approve';
    case QuotationsCancel = 'quotations.cancel';
    case QuotationsHistory = 'quotations.history';
    case QuotationsFollowUp = 'quotations.follow_up';
    case PurchaseOrdersView = 'purchase_orders.view';
    case PurchaseOrdersCreate = 'purchase_orders.create';
    case PurchaseOrdersUpdate = 'purchase_orders.update';
    case PurchaseOrdersConfirm = 'purchase_orders.confirm';
    case PurchaseOrdersCancel = 'purchase_orders.cancel';
}
