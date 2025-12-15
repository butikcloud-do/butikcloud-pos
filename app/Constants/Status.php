<?php

namespace App\Constants;

class Status
{

    const ENABLE  = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO  = 0;

    const VERIFIED   = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS  = 1;
    const PAYMENT_PENDING  = 2;
    const PAYMENT_REJECT   = 3;

    const TICKET_OPEN   = 0;
    const TICKET_ANSWER = 1;
    const TICKET_REPLY  = 2;
    const TICKET_CLOSE  = 3;

    const PRIORITY_LOW    = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH   = 3;

    const USER_ACTIVE = 1;
    const USER_BAN    = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING    = 2;
    const KYC_VERIFIED   = 1;

    const GOOGLE_PAY = 5001;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM  = 3;

    const DISCOUNT_PERCENT = 1;
    const DISCOUNT_FIXED   = 2;

    const TAX_TYPE_EXCLUSIVE = 1;
    const TAX_TYPE_INCLUSIVE = 2;

    const PRODUCT_TYPE_STATIC   = 1;
    const PRODUCT_TYPE_VARIABLE = 2;

    const PURCHASE_RECEIVED = 1;
    const PURCHASE_PENDING  = 2;
    const PURCHASE_ORDERED  = 3;

    const SALE_FINAL     = 1;
    const SALE_QUOTATION = 2;

    const SUPPER_ADMIN_ID     = 1;
    const SUPER_ADMIN_ROLE_ID = 1;

    const TRANSFER_SEND = 1;

    const PENDING  = 0;
    const APPROVED = 1;
    const REJECTED = 2;

    const MONTHLY     = 1;
    const YEARLY      = 2;

    const PLAN_PURCHASE_INITIATE = 0;
    const PLAN_PURCHASE_SUCCESS  = 1;
    const PLAN_PURCHASE_EXPIRED  = 2;

    const PLAN_ACTIVATE = 1;
    const PLAN_EXPIRED  = 3;
    const PLAN_ON_TRIAL = 4;

    const GATEWAY_PAYMENT = 2;

    const CASH_REGISTER_TYPE_SALE = 1;
    const CASH_REGISTER_OTHER_CREDIT = 2;
    const CASH_REGISTER_TYPE_EXPENSE  = 3;
    const CASH_REGISTER_OTHER_DEBIT = 4;
    const UNLIMITED = -1;
}
