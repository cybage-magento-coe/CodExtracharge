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
namespace Cybage\CodExtracharge\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

class CashondeliveryTable extends AbstractDb
{
    protected $storeManager;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init('cyb_codextracharge', 'id');
    }

    /**
     * Get fee from table
     *
     * @param double $amount
     * @param string $country
     * @param string $region
     * @return double
     */
    public function getFee($amount, $country)
    {
        if (!$country) {
            $country = '';
        }

        

        $table = $this->getMainTable();

        $currentWebsite = $this->storeManager->getWebsite()->getCode();


        $connection = $this->getConnection();
        $qry = $connection->select()
            ->from($table, '*')
            ->where(
                '('
                    .'country = '.$connection->quote($country).' OR '
                    .'country = '.$connection->quote('*')
                .') AND ('
                    .'amount_above < '.doubleval($amount).' AND ('
                        .'website = '.$connection->quote($currentWebsite).' OR '
                        .'website = '.$connection->quote('*')
                   .')'
                .')'
            )
            ->order('amount_above desc')
            ->order(new \Zend_Db_Expr("website = '*'"))
            ->order(new \Zend_Db_Expr("country = '*'"))
            ->limit(1);


        //die;
        $row = $connection->fetchRow($qry);
        if ($row) {
            if ($row['is_pct']) {
                return doubleval($amount) / 100.0 * doubleval($row['cod_charge']);
            }

            return doubleval($row['cod_charge']);
        }

        return 0;
    }

    /**
     * Get table as array
     *
     * @return array
     */
    public function getTableAsArray()
    {
        $table = $this->getMainTable();

        $connection = $this->getConnection();
        $query = $connection->select()
            ->from($table, '*');

        // @codingStandardsIgnoreStart
        return $connection->fetchAll($query);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Populate table from array
     *
     * @param array $data
     */
    public function populateFromArray(array $data)
    {
        $connection = $this->getConnection();
        $connection->beginTransaction();

        $table = $this->getMainTable();

        $connection->delete($table);
        foreach ($data as $dataRow) {
            $connection->insert($table, $dataRow);
        }

        $connection->commit();
    }
    
    public function populateFromTable(array $data)
    {
        $connection = $this->getConnection();
        

        $table = $this->getMainTable();
        foreach ($data as $dataRow) {
            $connection->beginTransaction();
            $qry = $connection->select()
              ->from($table, '*')
              ->where(
                  '('
                      .'country = '.$connection->quote($dataRow['country'])
                  .') AND ('
                          .'website = '.$connection->quote($dataRow['website'])
                  .')'
              )
            ->limit(1);
            $row = $connection->fetchRow($qry);
            if ($row['id']) {
                //echo $row['id'];
                //echo "update";
                
                $connection->update($table, $dataRow, ['id = ?' =>$row['id']]);
            } else {
               // echo "add";
                $connection->insert($table, $dataRow);
            }
            $connection->commit();
        }
      //  die;
    }

    /**
     * Get rows count
     *
     * @return int
     */
    public function getRowsCount()
    {
        return count($this->getTableAsArray());
    }
}
