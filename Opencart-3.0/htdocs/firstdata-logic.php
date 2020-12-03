<?php

namespace Admin\Controller\Extensions\Payment {
    interface FirstdataInterface {

    }

    interface FirstdataRemoteInterface {
        /**
         * check() for POST data
         * validate() it
         * process() errors
         * render() breadcrumbs
         * output() data to view.
         * 
         * @link /admin/controller/extension/payment/firstdata_remote.php
         * 
         * @return void
         */
        public function index();
        /**
         * model_extension_payment_firstdata_remote->install()
         * Admin\Model\Extension\Payment\FirstdataRemote->install()
         * 
         * @return void
         */
        public function install();
        /**
         * model_extension_payment_firstdata_remote->uninstall()
         * Admin\Model\Extension\Payment\FirstdataRemote->uninstall()
         * 
         * @return void
         */
        public function uninstall();
        public function order();
        public function void();
        public function capture();
        public function refund();
        protected function validate();
    }
}