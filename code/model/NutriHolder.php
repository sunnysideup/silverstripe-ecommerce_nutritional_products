<?php

class NutriHolder extends DataObject
{

    private static $singular_name = 'Nutritional Information Profile';
    public function i18n_singular_name()
    {
        return self::$singular_name;
    }

    private static $plural_name = 'Nutritional Information Profiles';
    public function i18n_plural_name()
    {
        return self::$plural_name;
    }

    private static $db = array(
        'ProfileName' => 'Varchar',
        'ServingCount' => 'Int',
        'Container' => 'Varchar(10)',
        'ServingSize' => 'Varchar(15)',
        'AdditionalInfo' => 'Varchar(100)'
    );

    private static $has_many = array(
        'NutriRows' => 'NutriRow',
        'Products' => 'ProductGroup',
        'ProductVariations' => 'ProductVariation'
    );

    /**
     * @inherited
     */
    private static $casting = array(
        'Title' => "Varchar",
        'Per100Unit' => "Varchar"
    );

    /**
     * Returns the unit for the 'Per 100' column of nutritional information. It tries
     * to get the unit from the ServingSize field, and if no match returns 'g'
     * @return String
     */
    function Per100Unit(){ return $this->getPer100Unit();}
    function getPer100Unit(){
        $string = trim($this->ServingSize);
        $matches = array();
        $matchResult = preg_match ('/ ?([A-Z]|[a-z]){1,7}/' ,$string, $matches);
        if (!$matchResult) return "g";
        return trim($matches[0]);
    }

    /**
     * @return String
     */
    function Title(){ return $this->getTitle();}
    function getTitle(){
        $string = $this->ProfileName;
        if(!$string) {
            $string = 'Profile #'.$this->ID.' (please customise) ';
        }
        $string .= ': ';
        $string .= "serving: ".$this->ServingCount."; ";
        $string .= "size: ".$this->ServingSize."; ";
        $string .= "container: ".$this->Container."; ";
        return $string;
    }

    private static $summary_fields = array(
        'Title' => 'Title',
    );

    private static $searchable_fields = array(
        'ServingCount' => 'ExactMatchFilter',
        'Container' => 'PartialMatchFilter',
        'ServingSize' => 'ExactMatchFilter',
        'ServingSizeUnit' => 'PartialMatchFilter',
        'AdditionalInfo' => 'PartialMatchFilter'
    );


    public function getCMSFields()
    {

        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RelationEditor::create();
        $config->addComponent(new GridFieldSortableRows('SortOrder'));

        $fields->addFieldsToTab (
            'Root.Main',
            array(
                NumericField::create('ServingCount', 'Servings per package')
                    ->setRightTitle('The number of servings in the jar/bucket/bottle/conatiner'),
                TextField::create('Container', 'The product container')
                    ->setRightTitle('The conatiner for the product e.g. Jar, Bottle, Bucket'),
                TextField::create('ServingSize', 'The size of each serving')
                    ->setRightTitle('The size of each serving e.g., 3g, 30ml'),
                TextField::create('AdditionalInfo', 'Additional information')
                    ->setRightTitle('For example "Remove label with care."'),
            )
        );

        $productsGrid = $fields->dataFieldByName('Products');
        if ($productsGrid) {
            $productsFieldConfig = GridFieldConfig_RecordViewer::create();
            $productsGrid -> setConfig($productsFieldConfig );
        }

        $productVariationsGrid = $fields->dataFieldByName('ProductVariations');
        if ($productVariationsGrid) {
            $productVariationsConfig = GridFieldConfig_RecordViewer::create();
            $productVariationsGrid -> setConfig($productVariationsConfig);
        }

        $nutriRowsGridField = $fields->dataFieldByName('NutriRows');

        if ($nutriRowsGridField) {
            $nutriRowsFieldConfig = $nutriRowsGridField ->getConfig();
            $nutriRowsFieldConfig
                ->addComponent(new GridFieldEditableColumns())
                ->addComponent(new GridFieldDeleteAction())
                ->addComponent(new GridFieldSortableRows('SortOrder'))
                ->getComponentByType('GridFieldEditableColumns')

                ->setDisplayFields(
                    array(
                        'Title' => array(
                            'title' => 'Item',
                            'field' => 'ReadonlyField'
                        ),
                        'PerServe'  => array(
                            "title" => "Per Serve",
                            "callback" => function($record, $column, $grid) { return new TextField($column, "Serve"); }),

                        'Per100' => function($record, $column, $grid) {return new TextField($column, "Per 100"); }

                    )
                );
        }
        $fields->removeFieldFromTab('Root.Main', 'SortOrder');

        return $fields;
    }

    /**
     * @return DataList
     */
    function ShownNutriRows()
    {
        return $this->NutriRows()
            ->exclude(array("Hide" => 1));
    }

}
