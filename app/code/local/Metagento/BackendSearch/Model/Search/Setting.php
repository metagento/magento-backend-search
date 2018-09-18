<?php

class Metagento_BackendSearch_Model_Search_Setting
    extends Varien_Object
{

    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $query = $this->getQuery();
        $data = array();

        $configSections = Mage::getSingleton('adminhtml/config')->getSections()->asArray();
        foreach ($configSections as $sectionId => $section) {
            $data[] = array(
                "id"               => "system_config/edit/section/$sectionId",
                "type"             => "Setting",
                "name"             => $section['label'],
                'form_panel_title' => $section['label'],
                "description"      => $section['label'],
                "url"              => Mage::helper('adminhtml')->getUrl("*/system_config/edit/section/$sectionId")
            );
            foreach ($section['groups'] as $groupId => $group) {
                $data[] = array(
                    "id"               => "system_config/edit/section/$sectionId",
                    "type"             => "Setting",
                    "name"             => $group['label'],
                    "form_panel_title" => $group['label'],
                    "description"      => $section['label'] . ' -> ' . $group['label'],
                    "url"              => Mage::helper('adminhtml')->getUrl("*/system_config/edit/section/$sectionId")
                );
                foreach ($group['fields'] as $fieldId => $field) {
                    $data[] = array(
                        "id"               => "system_config/edit/section/$sectionId",
                        "type"             => "Setting",
                        "name"             => $field['label'],
                        "form_panel_title" => $field['label'],
                        "description"      => $section['label'] . ' -> ' . $group['label'] . ' -> ' . $field['label'],
                        "url"              => Mage::helper('adminhtml')->getUrl("*/system_config/edit/section/$sectionId")
                    );
                }
            }
        }

        $configMenu = Mage::getConfig()
            ->loadModulesConfiguration('adminhtml.xml')
            ->applyExtends()
            ->getNode('menu')
            ->asArray();
        foreach ($configMenu as $id => $menu) {
            $data[] = array(
                "id"               => "menu/$id",
                "type"             => "Menu",
                "name"             => $menu['title'],
                "form_panel_title" => $menu['title'],
                "description"      => $menu['title'],
                "url"              => Mage::helper('adminhtml')->getUrl($menu['action'])
            );
            if (array_key_exists('children', $menu)) {
                foreach ($menu['children'] as $secondId => $secondMenu) {
                    $data[] = array(
                        "id"               => "menu/$id/$secondId",
                        "type"             => "Menu",
                        "name"             => $secondMenu['title'],
                        "form_panel_title" => $secondMenu['title'],
                        "description"      => $menu['title'] . ' -> ' . $secondMenu['title'],
                        "url"              => Mage::helper('adminhtml')->getUrl($secondMenu['action'])
                    );
                    if (array_key_exists('children', $secondMenu)) {
                        foreach ($secondMenu['children'] as $thirdId => $thirdMenu) {
                            $data[] = array(
                                "id"               => "menu/$id/$secondId/$thirdId",
                                "type"             => "Menu",
                                "name"             => $thirdMenu['title'],
                                "form_panel_title" => $thirdMenu['title'],
                                "description"      => $menu['title'] . ' -> ' . $secondMenu['title'] . ' -> ' . $thirdMenu['title'],
                                "url"              => Mage::helper('adminhtml')->getUrl($thirdMenu['action'])
                            );
                        }
                    }
                }
            }
        }

        foreach ($data as $index => $item) {
            if (strpos(strtolower($item['name']), strtolower($query)) === false) {
                unset($data[$index]);
            }
        }

        $this->setResults($data);

        return $this;
    }
}
