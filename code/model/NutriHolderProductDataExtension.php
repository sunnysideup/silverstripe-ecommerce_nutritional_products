<?php

class NutriHolderProductDataExtension extends DataExtension
{

    private static $has_one = array(
        'NutriHolder' => 'NutriHolder'
    );

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('NutriHolderID');
        if($this->owner->NutriHolder()->exists()){
            $config = new GridFieldConfig_RelationEditor();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $config->removeComponentsByType('GridFieldFilterHeader');
            $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
            
            $fields->addFieldToTab(
                'Root.Nutrition', 
                GridField::create(
                    'NutriHolder', 
                    'Nutritional Information', 
                    NutriHolder::get()->filter(array('ID' => $this->owner->NutriHolderID)),
                    $config
                )
            );
        } else {

            $fields->addFieldsToTab(
                'Root.Nutrition',
                array(
                    HasOneButtonField::create('NutriHolder', 'Nutritional Info', $this->owner),
                    HeaderField::create('SelectNutriHolderHeader', 'Select existing Nutritional Information Profile'),
                    DropDownField::create('NutriHolderID', 'Nutritional Info', array("" => "-- please select --") + NutriHolder::get()->map()->toArray())
                        ->setRightTitle('Select an existing Nutritional Information Profile'),
                )
            );
        }
    }
    

}
