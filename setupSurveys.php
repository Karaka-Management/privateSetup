<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Karaka
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use Modules\Surveys\Models\SurveyElementType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Create surveys
 */
//region Surveys
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Surveys\Controller\ApiController $module */
$module = $app->moduleManager->get('Surveys');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$SURVEY_COUNT = 50;
$FILLED_COUNT = 250;
$LOREM_COUNT  = \count(Text::LOREM_IPSUM) - 1;

$descriptionRng = new Text();

$count    = $SURVEY_COUNT;
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

for ($i = 0; $i < $SURVEY_COUNT; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

    $request->header->account = \mt_rand(1, 5);
    $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
    $request->setData('description', \preg_replace('/^.+\n/', '', $MARKDOWN));

    ($start = new \DateTime())->setTimestamp(\time() + \mt_rand(-100000000, 100000000));
    $request->setData('start', $start->format('Y-m-d H:i:s'));

    ($end = new \DateTime())->setTimestamp($start->getTimeStamp() + \mt_rand(1000, 100000000));
    $request->setData('end', $end->format('Y-m-d H:i:s'));

    //region files
    $files = \scandir(__DIR__ . '/media/types');

    $fileCounter = 0;
    $toUpload    = [];
    $mFiles      = [];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 76) {
            continue;
        }

        ++$fileCounter;

        if ($fileCounter === 1) {
            \copy(__DIR__ . '/media/types/' . $file, __DIR__ . '/temp/' . $file);

            $toUpload['file' . $fileCounter] = [
                'name'     => $file,
                'type'     => \explode('.', $file)[1],
                'tmp_name' => __DIR__ . '/temp/' . $file,
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/temp/' . $file),
            ];
        } else {
            $mFiles[] = \mt_rand(1, 9);
        }
    }

    if (!empty($toUpload)) {
        TestUtils::setMember($request, 'files', $toUpload);
    }

    if (!empty($mFiles)) {
        $request->setData('media', \json_encode(\array_unique($mFiles)));
    }
    //endregion

    $module->apiSurveyTemplateCreate($request, $response);
    ++$apiCalls;
    $sId = $response->get('')['response']->getId();

    $elements = [];

    $ELEMENT_COUNT = \mt_rand(7, 15);
    for ($j = 0; $j < $ELEMENT_COUNT; ++$j) {
    	$response = new HttpResponse();
    	$request  = new HttpRequest(new HttpUri(''));

    	$type = SurveyElementType::getRandom();

    	$MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_1-1');

    	$request->header->account = \mt_rand(1, 5);
    	$request->setData('type', $type);
    	$request->setData('text', \trim(\strtok($MARKDOWN, "\n"), ' #'));
    	$request->setData('description', $descriptionRng->generateText(25));
        $request->setData('survey', $sId);

    	$labels = [];
    	$values = [];

    	$count = 5; // otherwise it looks stupid, and surveys usually have a fixed set of columns
    	for ($m = 0; $m < $count; ++$m) {
	    	if ($type === SurveyElementType::CHECKBOX
	    		|| $type === SurveyElementType::RADIO
	    		|| $type === SurveyElementType::DROPDOWN
	    	) {
	    		$labels[] = Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)];
	    		$values[] = 'v_' . $m;
	    	}/* elseif ($type === SurveyElementType::TEXTFIELD
	    		|| $type === SurveyElementTyp::TEXTAREA
	    		|| $type === SurveyElementTyp::HEADLINE
	    		|| $type === SurveyElementTyp::NUMERIC
	    		|| $type === SurveyElementTyp::DATE
	    	) {
	    	}*/
	    }

        $elements[] = [
            'type'   => $type,
            'values' => $values,
        ];

    	$request->setData('labels', \json_encode($labels));
    	$request->setData('values', \json_encode($values));

        $module->apiSurveyTemplateElementCreate($request, $response);
        ++$apiCalls;
    }

    for ($k = 0; $k < $FILLED_COUNT; ++$k) {
    	$response = new HttpResponse();
    	$request  = new HttpRequest(new HttpUri(''));

    	$request->header->account = \mt_rand(1, 5);
    	$request->setData('survey', $sId);

    	for ($l = 0; $l < $ELEMENT_COUNT; ++$l) {
    		$value = '';
	    	if ($elements[$l] === SurveyElementType::CHECKBOX
                || $elements[$l] === SurveyElementType::RADIO
	    		|| $elements[$l] === SurveyElementType::DROPDOWN
	    	) {
                $value = $elements[$l]['values'][\mt_rand(0, \count($elements[$l]['values']) - 1)];
	    	} elseif ($elements[$l] === SurveyElementType::TEXTFIELD
	    		|| $elements[$l] === SurveyElementType::TEXTAREA
            ) {
                $value = Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)];
            } elseif ($elements[$l] === SurveyElementType::NUMERIC) {
                $value = \mt_rand(-100, 100);
            } elseif ($elements[$l] === SurveyElementType::DATE) {
                $value = \date('Y-m-d H:i:s', \mt_rand(1248531847, 1753453447));
	    	}

    		$request->setData('e_' . $l, $value);
    	}

        $module->apiSurveyAnswerCreate($request, $response);
        ++$apiCalls;
    }

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 10 - $p);
//endregion
