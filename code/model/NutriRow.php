<?php

class NutriRow extends DataObject
{

    private static $default_rows = array(
        "Energy",
        "Protein",
        "Fat",
        "saturated",
        "trans fat",
        "polyunsaturated",
        "monounsaturated",
        "Cholesterol",
        "Carbohydrate",
        "sugars",
        "Dietary fibre",
        "Sodium"
    );

    private static $singular_name = 'Nutritional Information Item';
    public function i18n_singular_name()
    {
        return self::$singular_name;
    }

    private static $plural_name = 'Nutritional Information Items';
    public function i18n_plural_name()
    {
        return self::$plural_name;
    }

    private static $db = array(
        'Title' => 'Varchar(30)',
        'PerServe' => 'Varchar(20)',
        'Per100' => 'Varchar(20)',
        'Hide' => 'Boolean',
        'SortOrder' => 'Int',
    );

    private static $has_one = array(
        'NutriHolder' => 'NutriHolder',
    );

    private static $default_sort = array(
        'SortOrder' => 'ASC',
    );

    private static $summary_fields = array(
        'Title' => 'Title',
        'PerServe' => 'Per Serve',
        'Per100' => 'Per 100',
        'Hide.Nice' => 'Hidden'
    );

    private static $indexes = array(
        'SortOrder' => true
    );

    public function Shown() {
        return !$this->Hide;
    }

    /**
     *
     *
     * @inherited
     */
    public function canDelete($member = null)
    {
        $defaultRows = Config::inst()->get("NutriRow", "default_rows");
        $defaultRows = array_map('strtolower', $defaultRows);

        if (in_array(strtolower($this->Title), $defaultRows)) {
            return false;
        }
        return parent::canDelete($member);
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab (
            'Root.Main',
            array(
                CheckboxField::create('Hide', 'Hide entry')
                    ->setRightTitle('Hide this entry - not relevant ... '),
                TextField::create('Title', 'Nutritional information item')
                    ->setRightTitle('E.g. salt, carbohydrates, ebergy'),
                TextField::create('PerServe', 'The amount of the item per serve')
                    ->setRightTitle('For example, 1g or 2,000KJ'),
                TextField::create('Per100', 'The amount of the item per 100g')
                    ->setRightTitle('For example, 1g or 2,000KJ'),
            )
        );

        $fields->removeFieldFromTab('Root.Main', 'SortOrder');
        $fields->removeFieldFromTab('Root.Main', 'NutriHolder');
        $fields->removeFieldFromTab('Root.Main', 'NutriHolderID');

        return $fields;
    }


    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $defaultRows = Config::inst()->get("NutriRow", "default_rows");


        $holders = NutriHolder::get();
        foreach($holders as $holder) {
            $sortOrder = 0;
            foreach($defaultRows as $itemName) {
                $sortOrder++;
                $filter = array(
                    "NutriHolderID" => $holder->ID,
                    "Title" => $itemName
                );
                $obj = NutriRow::get()->filter($filter)->first();
                if( ! $obj) {
                    DB::alteration_message("Creating $itemName", "created");
                    $obj = NutriRow::create($filter);
                    $obj->SortOrder = $sortOrder;
                }
                $obj->write();
            }

        }

    }



}
