<?php
namespace ActionEaseKit\Base\Exception;
/*
 * 402 Payment Required
 * Reserved for future use. The original intention was that this code might be used as part of some
 * form of digital cash or micropayment scheme, as proposed for example by GNU Taler[36],
 * but that has not yet happened, and this code is not usually used. Google Developers API
 * uses this status if a particular developer has exceeded the daily limit on requests.[37]
 * Sipgate uses this code if an account does not have sufficient funds to start a call.[38]
 * Shopify uses this code when the store has not paid their fees and is temporarily disabled. [39]
 *
 */
class App402Exception extends \Exception {}
