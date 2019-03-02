<?php
namespace Benlau\TrustedShopsWidget\Block\Widget;

class TrustedShopsWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'widget/trustedshopswidget.phtml';
    protected $_dir;

    const DEFAULT_TS_ID = 'X5FC10AED8EC57F5C17A3316FB3B5F76A';

    /**
    * @param \Magento\Framework\View\Element\Template\Context $context
    * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
    * @param array $data
    */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        parent::__construct($context);
        $this->_dir = $dir;
    }

    public function getTsId() {
        if ($this->getData('trustedshopsid') == '')
            return DEFAULT_TS_ID;
        else
            return $this->getData('trustedshopsid');
    }

    public function getDisplayType() {
        if($this->getData('display_type') == '')
            return 'image';
        else
            return $this->getData('display_type');
    }

    private function trustedshopscachecheck($filename_cache, $timeout = 10800) {
        if (file_exists($filename_cache) && time() - filemtime($filename_cache) < $timeout) {
            return true;
        }
        return false;
    }

    public function getTsData() {
        $tsId = $this->getTsId();
        $tmpDir = $this->_dir->getPath('tmp'); // Output: /var/www/html/myproject/var/tmp
        $cacheFileName = $tmpDir . '/' . $tsId . '.json';
        $cacheTimeOut = 43200; // half a day
        $apiUrl = 'https://api.trustedshops.com/rest/public/v2/shops/' . $tsId . '/quality/reviews.json';
        $reviewsFound = false;

        // check if cached version exists
        if (!$this->trustedshopscachecheck($cacheFileName, $cacheTimeOut)) {
            // load fresh from API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1); // times out after 4s
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            $output = curl_exec($ch); curl_close($ch);
            // Write the contents back to the file
            // Make sure you can write to file's destination
            file_put_contents($cacheFileName, $output);
        }
        if ($jsonObject = json_decode(file_get_contents($cacheFileName), true)) {
            $result = $jsonObject['response']['data']['shop']['qualityIndicators']['reviewIndicator']['overallMark'];
            $count = $jsonObject['response']['data'] ['shop']['qualityIndicators']['reviewIndicator']['activeReviewCount'];
            $shopName = $jsonObject['response']['data']['shop']['name'];
            $max = "5.00";
            if ($count > 0) {
                $reviewsFound = true;
            }
        }

        if ($reviewsFound) {
            $tsData['result'] = $result;
            $tsData['count'] = $count;
            $tsData['shopName'] = $shopName;
            $tsData['max'] = $max;

            return $tsData;
        } else
            return false;
    }


    public function getTsShopReviews() {
        $tsId = $this->getTsId();
        $tmpDir = $this->_dir->getPath('tmp'); // Output: /var/www/html/myproject/var/tmp
        $cacheFileName = $tmpDir . '/' . $tsId . '-shop-reviews.json';
        $cacheTimeOut = 43200; // half a day
        $apiUrl = 'https://api.trustedshops.com/rest/public/v2/shops/' . $tsId . '/reviews.json';

        // check if cached version exists
        if (!$this->trustedshopscachecheck($cacheFileName, $cacheTimeOut)) {
            // load fresh from API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1); // times out after 4s
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            $output = curl_exec($ch); curl_close($ch);
            // Write the contents back to the file
            // Make sure you can write to file's destination
            file_put_contents($cacheFileName, $output);
        }
        if ($jsonObject = json_decode(file_get_contents($cacheFileName), true)) {
            $reviews = $jsonObject['response']['data']['shop']['reviews'];

            $i=0;

            foreach($reviews as $review) {
                $date = $review['creationDate'];
                $comment = $review['comment'];
                $date = date("d.m.Y", strtotime($date));

                echo 'Datum: ' . $date . '<br>';
                echo 'Kommentar: ' . $comment . '<br>';

                foreach($review['criteria'] as $criteria) {
                    $mark = $criteria['mark'];

                    switch($criteria['type']) {
                        case 'DELIVERY':
                            $criteria_title = 'Lieferung';
                            break;
                        case 'GOODS':
                            $criteria_title = 'Ware';
                            break;
                        case 'SERVICE':
                            $criteria_title = 'Service';
                            break;
                        default:
                            $criteria_title = 'Gesamt';
                    }

                    echo $criteria_title . ': ' . $mark . '<br>';
                }

                $overall_mark = $review['mark'];

                echo 'Gesamt: ' . $overall_mark . '<br><br>';

                $i++;
                if($i==5) break;
            }
        }
    }

    public function getTsSnippet() {
        $tsData = $this->getTsData();

        if ($tsData !== false) {
            // Output aggregated reviews markup:
            $ts_snippet = '<script type="application/ld+json">';
            $ts_snippet .= '{';
            $ts_snippet .= '"@context": "http://schema.org",';
            $ts_snippet .= '"@type": "Organization",';
            $ts_snippet .= '"name": "' . $tsData['shopName'] . '",';
            $ts_snippet .= '"aggregateRating" : {';
            $ts_snippet .= '"@type": "AggregateRating",';
            $ts_snippet .= '"ratingValue" : "' . $tsData['result'] . '",';
            $ts_snippet .= '"bestRating" : "' . $tsData['max'] . '",';
            $ts_snippet .= '"ratingCount" : "' . $tsData['count'] . '"';
            $ts_snippet .= '}}';
            $ts_snippet .= '</script>';

            return $ts_snippet;
        } else {
            return 'error';
        }
    }
}