<?php

namespace PartySupplies\Customer\Helper;

class Constant
{
    /**
     * Company creation url
     */
    const COMPANY_CREATE_URL = 'company/account/create';

    /**
     * Company/User creation controller
     */
    const ACCOUNT_CREATE_POST_URL = 'company/account/createpost';

    /**
     * Company approved email template path
     */
    const COMPANY_APPROVED_EMAIL_TEMPLATE = 'customer/create_account/company_approved_email_template';

    /**
     * Company declined email template path
     */
    const COMPANY_DECLINED_EMAIL_TEMPLATE =  'customer/create_account/company_declined_email_template';

    /**
     * Company keyword
     */
    const COMPANY = 'company';

    /**
     * Customer keyword
     */
    const CUSTOMER = 'customer';

    /**
     * Customer context
     */
     const CONTEXT_CUSTOMER_ID = 'customer_id';
}
