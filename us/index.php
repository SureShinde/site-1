<?php
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList as DL;
use Magento\Store\Model\StoreManager as SM;

try {
    require dirname(__DIR__) . '/app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    exit(1);
}

// 2019-03-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
// «Remove the primary store's code from URLs»
// https://www.upwork.com/ab/f/contracts/21683566
$_SERVER = [
	SM::PARAM_RUN_CODE => 'us', SM::PARAM_RUN_TYPE => 'website'
	,Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => [
		DL::PUB => [DL::PATH => dirname(__DIR__)],
	]
] + $_SERVER;

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);
