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

$GLOBALS['TL_DCA']['tl_wem_job'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ctable' => ['tl_wem_job_application'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['code'],
            'flag' => 1,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['code', 'title'],
            'format' => '%s | %s',
        ],
        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ],
            'copy' => [
                'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
            ],
            'toggle' => [
                'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['toggle'],
                'icon' => 'visible.svg',
                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => ['tl_wem_job', 'toggleIcon'],
                'showInHeader' => true,
            ],
            'applications' => [
                'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['applications'],
                'href' => 'table=tl_wem_job_application',
                'icon' => 'folderOP.gif',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '
            {title_legend},code,title,postedAt,availableAt;
            {location_legend},countries,locations;
            {details_legend},field,remuneration,status;
            {content_legend},text,file;
            {hr_legend},hrName,hrPosition,hrPhone,hrEmail;
            {publish_legend},published,start,stop
        ',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'createdAt' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['createdAt'],
            'default' => time(),
            'flag' => 8,
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],

        'code' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['code'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['title'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'postedAt' => [
            'exclude' => true,
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['postedAt'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'availableAt' => [
            'exclude' => true,
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['availableAt'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'countries' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['countries'],
            'exclude' => true,
            'filter' => true,
            'sorting' => true,
            'inputType' => 'select',
            'eval' => ['multiple' => true, 'chosen' => true],
            'options_callback' => function () {
                return System::getCountries();
            },
            'sql' => 'blob NULL',
        ],
        'locations' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['locations'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'listWizard',
            'sql' => 'blob NULL',
        ],

        'field' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['field'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'remuneration' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['remuneration'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'status' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['status'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        'hrName' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['hrName'],
            'default' => BackendUser::getInstance()->name,
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'hrPosition' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['hrPosition'],
            'default' => '',
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'hrPhone' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['hrPhone'],
            'default' => '',
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'hrEmail' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['hrEmail'],
            'default' => BackendUser::getInstance()->email,
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        'text' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['text'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['mandatory' => true, 'rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'],
            'explanation' => 'insertTags',
            'sql' => 'mediumtext NULL',
        ],
        'file' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['file'],
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL',
        ],

        'published' => [
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['published'],
            'exclude' => true,
            'filter' => true,
            'flag' => 1,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'start' => [
            'exclude' => true,
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['start'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'stop' => [
            'exclude' => true,
            'label' => &$GLOBALS['TL_LANG']['tl_wem_job']['stop'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
    ],
];

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class tl_wem_job extends Backend
{
    /**
     * Import the back end user object.
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * Return the "toggle visibility" button.
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (\strlen(Input::get('tid'))) {
            $this->toggleVisibility(Input::get('tid'), (1 == Input::get('state')), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->hasAccess('tl_wem_job::published', 'alexf')) {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published']) {
            $icon = 'invisible.svg';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="'.($row['published'] ? 1 : 0).'"').'</a> ';
    }

    /**
     * Disable/enable a job.
     *
     * @param int           $intId
     * @param bool          $blnVisible
     * @param DataContainer $dc
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc = null): void
    {
        // Set the ID and action
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');

        if ($dc) {
            $dc->id = $intId; // see #8043
        }

        // Trigger the onload_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_wem_job']['config']['onload_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_wem_job']['config']['onload_callback'] as $callback) {
                if (\is_array($callback)) {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                } elseif (\is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        // Check the field access
        if (!$this->User->hasAccess('tl_wem_job::published', 'alexf')) {
            throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to publish/unpublish job item ID '.$intId.'.');
        }

        // Set the current record
        if ($dc) {
            $objRow = $this->Database->prepare('SELECT * FROM tl_wem_job WHERE id=?')
                                     ->limit(1)
                                     ->execute($intId)
            ;

            if ($objRow->numRows) {
                $dc->activeRecord = $objRow;
            }
        }

        $objVersions = new Versions('tl_wem_job', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_wem_job']['fields']['published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_wem_job']['fields']['published']['save_callback'] as $callback) {
                if (\is_array($callback)) {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
                } elseif (\is_callable($callback)) {
                    $blnVisible = $callback($blnVisible, $dc);
                }
            }
        }

        $time = time();

        // Update the database
        $this->Database->prepare("UPDATE tl_wem_job SET tstamp=$time, published='".($blnVisible ? '1' : '')."' WHERE id=?")
                       ->execute($intId)
        ;

        if ($dc) {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->published = ($blnVisible ? '1' : '');
        }

        // Trigger the onsubmit_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_wem_job']['config']['onsubmit_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_wem_job']['config']['onsubmit_callback'] as $callback) {
                if (\is_array($callback)) {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                } elseif (\is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        $objVersions->create();
    }
}
