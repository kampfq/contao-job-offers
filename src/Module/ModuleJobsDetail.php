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
class ModuleJobsDetail extends ModuleJobsList
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_jobsdetail';

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
        \System::getCountries();

        $intJob = \Input::get('id');

        $objJob = JobModel::findByPk($intJob);

        $this -> Template -> field = $objJob -> field;
        $this -> Template ->  status = $objJob -> status;
        $this -> Template ->  remuneration = $objJob -> remuneration;
        $this -> Template ->  text = $objJob -> text;
        $this -> Template ->  title = $objJob -> title;


        if ('' !== $objJob->cssClass) {
            $strClass = ' '.$objJob->cssClass;
        }

        // Add the meta information
        $this->Template ->date = (int) $objJob->postedAt;
        $this->Template ->timestamp = $objJob->postedAt;
        $this->Template ->datetime = date('Y-m-d\TH:i:sP', (int) $objJob->postedAt);

        // Parse locations
        if (deserialize($objJob->locations)) {
            $this->Template -> locations = implode(', ', deserialize($objJob->locations));
        }

        // Fetch the job offer file
        if ($objFile = \FilesModel::findByUuid($objArticle->file)) {
            $this->Template ->file = $objFile->path;
        } else {
            $this->Template ->file = null;
        }

        // Tag the response
        if (\System::getContainer()->has('fos_http_cache.http.symfony_response_tagger')) {
            /** @var ResponseTagger $responseTagger */
            $responseTagger = \System::getContainer()->get('fos_http_cache.http.symfony_response_tagger');
            $responseTagger->addTags(['contao.db.tl_pzl_job.'.$objJob->id]);
        }

        //form
        // If we have setup a form, allow module to use it later
        if ($this->job_applicationForm) {
            $this->blnDisplayApplyButton = true;
          //  $('.modalFW[data-name="jobApplyModal"] input[name="tstamp"]').val(Math.floor(Date.now() / 1000));
          //  $('.modalFW[data-name="jobApplyModal"] input[name="createdAt"]').val(Math.floor(Date.now() / 1000));
            $this -> Template ->jobID = $objJob->id;
            $this -> Template -> form = $this->getForm($this->job_applicationForm);

        }

    }

    /**
     * Parse and return an application form for a job.
     *
     * @param int    $intJob      [Job ID]
     * @param string $strTemplate [Template name]
     *
     * @return string
     */
    protected function getApplicationForm($objJob, $strTemplate = 'job_apply')
    {
        $strForm = $this->getForm($this->job_applicationForm);

        $objTemplate = new \FrontendTemplate($strTemplate);
        $objTemplate->id = $objJob->id;
        $objTemplate->code = $objJob->code;
        $objTemplate->title = $objJob->title;
        $objTemplate->recipient = $objJob->hrEmail ?: $GLOBALS['TL_ADMIN_EMAIL'];
        $objTemplate->time = time();
        $objTemplate->token = \RequestToken::get();
        $objTemplate->form = $strForm;

        return $objTemplate->parse();
    }

}
