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

namespace WEM\JobOffersBundle\Hooks;

use WEM\JobOffersBundle\Model\Job as JobModel;

class StoreFormDataHook
{
    public function storeFormData($arrSet, $objForm)
    {
        try {
            if ('job-offer-application' === $objForm->alias) {
                // Unset fields who are not in tl_wem_job_application table
                $objJob = JobModel::findByPk($arrSet['pid']);

                $strCode = $objJob->code;
                unset($arrSet['recipient'], $arrSet['code'], $arrSet['title']);

                // Convert files path into uuid
                if ($arrSet['cv'] && $objFile = \FilesModel::findOneByPath($arrSet['cv'])) {
                    $arrSet['cv'] = $objFile->uuid;

                    // Move file into a subfolder with a clearer name
                    // Rule: {form_folder}/{job_code}/{cv_lastname_firtname}
                    $strNewName = sprintf(
                        '%s/cv_%s_%s_%s.%s',
                        $strCode,
                        \StringUtil::generateAlias($arrSet['lastname']),
                        \StringUtil::generateAlias($arrSet['firstname']),
                        date('Y-m-d_H-i'),
                        $objFile->extension
                    );
                    $strFilename = str_replace($objFile->name, $strNewName, $objFile->path);

                    $objFileCV = new \File($objFile->path);
                    $objFileCV->renameTo($strFilename);
                }
                if ($arrSet['applicationLetter'] && $objFile = \FilesModel::findOneByPath($arrSet['applicationLetter'])) {
                    $arrSet['applicationLetter'] = $objFile->uuid;

                    // Move file into a subfolder with a clearer name
                    // Rule: {form_folder}/{job_code}/{cv_lastname_firtname}
                    $strNewName = sprintf(
                        '%s/al_%s_%s_%s.%s',
                        $strCode,
                        \StringUtil::generateAlias($arrSet['lastname']),
                        \StringUtil::generateAlias($arrSet['firstname']),
                        date('Y-m-d_H-i'),
                        $objFile->extension
                    );
                    $strFilename = str_replace($objFile->name, $strNewName, $objFile->path);

                    $objFileAP = new \File($objFile->path);
                    $objFileAP->renameTo($strFilename);
                }


                //generate XML
                $strNewName = sprintf(
                    'files/applications/%s/al_%s_%s_%s.%s',
                    $strCode,
                    \StringUtil::generateAlias($arrSet['lastname']),
                    \StringUtil::generateAlias($arrSet['firstname']),
                    date('Y-m-d_H-i'),
                   'xml'
                );

                $xml_header = '<?xml version="1.0" encoding="UTF-8"?><application></application>';
                $xml = new \SimpleXMLElement($xml_header);

                foreach ($arrSet as $key => $value) {
                    if($key === 'cv'){
                        $xml->addChild($key, $objFileCV -> name);

                    } elseif ($key === 'applicationLetter'){
                        $xml->addChild($key, $objFileAP -> name);

                    } else {
                        $xml->addChild($key, $value);
                    }
                }

                $meh = new \File($strNewName);

                $dom = dom_import_simplexml($xml)->ownerDocument;
                $dom->formatOutput = true;
                $meh -> write($dom->saveXML());
                $meh -> close();

                // Clean the session
                $objSession = \Session::getInstance();
                $objSession->set('wem_job_offer', '');
            }

            return $arrSet;
        } catch (\Exception $e) {
            // @todo Translate error message
            \System::log(vsprintf('Exception lancée avec le message %s et la trace %s', [$e->getMessage(), $e->getTrace()]), __METHOD__, 'WEM_JOBOFFERS');
        }
    }
}
