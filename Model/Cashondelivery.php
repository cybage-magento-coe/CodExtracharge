<?php
/**
 * Cybage CodExtracharge
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available on the World Wide Web at:
 * http://opensource.org/licenses/osl-3.0.php
 * If you are unable to access it on the World Wide Web, please send an email
 * To: Support_ecom@cybage.com.  We will send you a copy of the source file.
 *
 * @category  Apply_Extra_Charge_On_COD_Payment_Method
 * @package   Cybage_CodExtracharge
 * @author    Cybage Software Pvt. Ltd. <Support_ecom@cybage.com>
 * @copyright 1995-2017 Cybage Software Pvt. Ltd., India
 *            http://www.cybage.com/pages/centers-of-excellence/ecommerce/ecommerce.aspx
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cybage\CodExtracharge\Model;

use Cybage\CodExtracharge\Api\CashondeliveryInterface;
use Cybage\CodExtracharge\Model\CashondeliveryCart;
use Cybage\CodExtracharge\Api\CashondeliveryTableInterface;

/**
 * Cashondelivery
 *
 * @category  Class
 * @package   Cybage_CodExtracharge
 * @author    Cybage Software Pvt. Ltd. <Support_ecom@cybage.com>
 * @copyright 1995-2017 Cybage Software Pvt. Ltd., India
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   Release: 1.0.0
 * @link      http://www.cybage.com/pages/centers-of-excellence/ecommerce/ecommerce.aspx
 */

class Cashondelivery implements CashondeliveryInterface
{

    /**
     * Constructor
     *
     * @param CashondeliveryCart $cashondeliveryCart
     */
    public function __construct(
        CashondeliveryCart  $cashondeliveryCart,
        CashondeliveryTableInterface $cashondeliveryTableInterface
    ) {
        $this->cashondeliveryCart = $cashondeliveryCart;
        $this->cashondeliveryTableInterface = $cashondeliveryTableInterface;
    }

    /**
     * Get cart information
     *
     * @return type
     */
    public function getCartInformation()
    {
        return $this->cashondeliveryCart;
    }

    /**
     * Get base amount
     * @param array $totals
     * @param string $country
     * @param string $region
     * @return double
     */
    public function getBaseAmount(array $totals, $country)
    {
        $calcBase = $this->getCalcBase($totals);
        return $this->cashondeliveryTableInterface->getCodCharge($calcBase, $country);
    }

    /**
     * Get calculation base
     * @param array $totals
     * @return double
     */
    public function getCalcBase(array $totals)
    {
       // $usedTotals = $this->getUsedTotals();

        $calcBase = 0;
        foreach ($totals as $totalCode => $total) {
//            if (!in_array($totalCode, $usedTotals)) {
//                continue;
//            }

            $calcBase += $total;
        }

        return $calcBase;
    }
}
