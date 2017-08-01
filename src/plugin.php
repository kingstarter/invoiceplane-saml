<?php

namespace KingStarter\InvoicePlaneSaml;

class Plugin 
{
    public function initialize() 
    {

    }

    public function getPluginDescription()
    {
        return 'SAML authentication plugin to let InvoicePlane act as a SAML service provider.';
    }

    public function getPluginAuthor()
    {
        return 'KingStarter GbR';
    }

    public function getPluginVersion()
    {
        return '0.0.1';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kingstarter/invoiceplane-saml';
    }
}
