<?php

declare(strict_types=1);

/**
 * Contao Job Offers for Contao Open Source CMS
 * Copyright (c) 2018-2020 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-job-offers
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-job-offers/
 */

namespace WEM\JobOffersBundle\Module;

use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\Input;
use Patchwork\Utf8;
use WEM\JobOffersBundle\Model\Job as JobModel;

/**
 * Front end module "offers list".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class ModuleJobsList extends \Module
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_jobslist';

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['jobslist'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        // Load bundles
        $this->bundles = \System::getContainer()->getParameter('kernel.bundles');

        return parent::generate();
    }

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        // Init countries
        \System::getCountries();

        if ($this->job_applicationDetail) {
            $this->blnDisplayDetailButton = true;
        }

        global $objPage;
        $limit = null;
        $offset = (int) $this->skipFirst;

        // Maximum number of items
        if ($this->numberOfItems > 0) {
            $limit = $this->numberOfItems;
        }

        $arrConfig = ['published' => 1];
        $this->Template->articles = [];
        $this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];

        // Get the available filters
        $objJobFilters = JobModel::findItems(['published' => 1]);
        if ($objJobFilters && 0 < $objJobFilters->count()) {

            $jobsFilteredByLocation = [];
            $arrJobFilters = [];
            $arrFieldFilters = [];
            $arrLocationFilters = [];
            $arrJobsByLocation = [
                'alle' => []
            ];
            $arrJoblocations = ['alle' => 'Alle'];
            while ($objJobFilters->next()) {
                if ('' !== $objJobFilters->title && !\in_array($objJobFilters->title, $arrJobFilters, true)) {
                    $arrJobFilters[] = $objJobFilters->title;
                }

                if ('' !== $objJobFilters->field && !\in_array($objJobFilters->field, $arrFieldFilters, true)) {
                    $arrFieldFilters[] = $objJobFilters->field;
                }

                $joblocations = deserialize($objJobFilters->locations);
                if (!$joblocations) {
                    continue;
                }
                array_push($arrJobsByLocation['alle'],$this->parseArticle($objJobFilters)) ;
                foreach ($joblocations as $location) {
                    if (!\in_array($location, $arrJoblocations, true)) {
                        $arrJoblocations[strtolower($location)] = $location;
                    }
                    if(!array_key_exists(strtolower($location),$arrJobsByLocation)){
                        $arrJobsByLocation[strtolower($location)] = [];
                    }
                    array_push($arrJobsByLocation[strtolower($location)],$this->parseArticle($objJobFilters)) ;
                }

                $arrCountries = deserialize($objJobFilters->countries);
                if (!$arrCountries) {
                    continue;
                }

                foreach ($arrCountries as $c) {
                    if (!\in_array($c, $arrLocationFilters, true)) {
                        $arrLocationFilters[$c] = $GLOBALS['TL_LANG']['CNT'][$c];
                    }
                }
            }
            $this->Template->jobFilters = $arrJobFilters;
            $this->Template->fieldFilters = $arrFieldFilters;
            $this->Template->countryFilters = $arrLocationFilters;
            $this->Template->locationFilters = $arrJoblocations;

            $this->Template->jobsByLocation = $arrJobsByLocation;
        }

        $objArticles = JobModel::findItems($arrConfig, ($limit ?: 0), ($offset ?: 0));

        // Add the articles
        if (null !== $objArticles) {
            $this->Template->articles = $this->parseArticles($objArticles);
        }

    }

    /**
     * Parse one or more items and return them as array.
     *
     * @param Model\Collection $objArticles
     * @param bool             $blnAddArchive
     *
     * @return array
     */
    protected function parseArticles($objArticles, $blnAddArchive = false)
    {
        $limit = $objArticles->count();

        if ($limit < 1) {
            return [];
        }

        $count = 0;
        $arrArticles = [];

        while ($objArticles->next()) {
            /** @var NewsModel $objArticle */
            $objArticle = $objArticles->current();

            $arrArticles[] = $this->parseArticle($objArticle, $blnAddArchive, ((1 === ++$count) ? ' first' : '').(($count === $limit) ? ' last' : '').((0 === ($count % 2)) ? ' odd' : ' even'), $count);
        }

        return $arrArticles;
    }

    /**
     * Parse an item and return it as string.
     *
     * @param NewsModel $objArticle
     * @param bool      $blnAddArchive
     * @param string    $strClass
     * @param int       $intCount
     *
     * @return string
     */
    protected function parseArticle($objArticle, $blnAddArchive = false, $strClass = '', $intCount = 0)
    {
        $objTemplate = new \FrontendTemplate($this->job_template);
        $objTemplate->setData($objArticle->row());

        if ('' !== $objArticle->cssClass) {
            $strClass = ' '.$objArticle->cssClass.$strClass;
        }

        $objTemplate->class = $strClass;
        $objTemplate->count = $intCount; // see #5708

        // Add the meta information
        $objTemplate->date = (int) $objArticle->postedAt;
        $objTemplate->timestamp = $objArticle->postedAt;
        $objTemplate->datetime = date('Y-m-d\TH:i:sP', (int) $objArticle->postedAt);

        // Parse locations
        if (deserialize($objArticle->locations)) {
            $objTemplate->locations = implode(', ', deserialize($objArticle->locations));
        }

        // Fetch the job offer file
        if ($objFile = \FilesModel::findByUuid($objArticle->file)) {
            $objTemplate->file = $objFile->path;
        } else {
            $objTemplate->file = null;
        }

        // Notice the template if we want/can display apply button
        if ($this->blnDisplayDetailButton) {
            $objTemplate->blnDisplayDetailButton = true;

            $objTemplate->detailUrl =  \Contao\PageModel::findById($this-> job_applicationDetail)->getFrontendUrl() .'?id='.$objArticle->id;

            // Comply with i18nl10n constraints
            if (\array_key_exists('VerstaerkerI18nl10nBundle', $this->bundles)) {
                $objTemplate->applyUrl = $GLOBALS['TL_LANGUAGE'].'/'.$objTemplate->applyUrl;
            }
        }

        // Notice the template if we want/can display apply button
        if ($this->blnDisplayApplyButton) {
            $objTemplate->blnDisplayApplyButton = true;
            $objTemplate->applyUrl = $this->addToUrl('apply='.$objArticle->id, true, ['job']);

            // Comply with i18nl10n constraints
            if (\array_key_exists('VerstaerkerI18nl10nBundle', $this->bundles)) {
                $objTemplate->applyUrl = $GLOBALS['TL_LANGUAGE'].'/'.$objTemplate->applyUrl;
            }
        }

        // Notice the template if we want to display the text
        if ($this->job_displayTeaser) {
            $objTemplate->blnDisplayText = true;
        } else {
            $objTemplate->detailsUrl = $this->addToUrl('seeDetails='.$objArticle->id, true, ['job']);

            // Comply with i18nl10n constraints
            if (\array_key_exists('VerstaerkerI18nl10nBundle', $this->bundles)) {
                $objTemplate->detailsUrl = $GLOBALS['TL_LANGUAGE'].'/'.$objTemplate->detailsUrl;
            }
        }

        // Notify the template we must open this item apply modal
        if ($this->openApplyModalOnStart && $objArticle->id === $this->openApplyModalOnStart) {
            $objTemplate->openApplyModalOnStart = true;
        }

        // Tag the response
        if (\System::getContainer()->has('fos_http_cache.http.symfony_response_tagger')) {
            /** @var ResponseTagger $responseTagger */
            $responseTagger = \System::getContainer()->get('fos_http_cache.http.symfony_response_tagger');
            $responseTagger->addTags(['contao.db.tl_pzl_job.'.$objArticle->id]);
        }

        return $objTemplate->parse();
    }
}
