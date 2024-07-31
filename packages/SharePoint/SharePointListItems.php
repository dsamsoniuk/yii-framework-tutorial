<?php

namespace app\components\SharePoint;

use Office365\SharePoint\FieldUserValue;
use Office365\SharePoint\ListItemCollection;
use Office365\SharePoint\ListItem;

/**
 * SharePoint Elementy/Itemy/Zadania/Taski wg Listy
 */
class SharePointListItems {
    public $spApi;

    /**
     * @param InterfaceSharePoint $spApi
     */
    public function __construct(InterfaceSharePoint $spApi) {
        $this->spApi = $spApi;
    }
    /**
     * Pobierz liste elementow/item wg listy
     * @param string $listName
     * @return [ListItem]
     */
    public function getItems(string $listName): array{

        $data = [];
        try {
            $client = $this->spApi->getClient();
            $list   = $client->getWeb()->getLists()->getByTitle($listName);
            $items  = $list->getItems()->get()->executeQuery();

            /** @var ListItem $item  */
            foreach ($items as $item) {
                $data[] = $item;
            }
        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
            return [];
        }

        return $data;
    }
    /**
     * Pobierz element/item z listy
     * @param string $listName
     * @param int $sharePointId ListItemID
     * @return ListItem|null
     */
    public function getItem(string $listName, int $sharePointId): ListItem|null{
        $listItem = null;

        try {
            $client = $this->spApi->getClient();
            $list   = $client->getWeb()->getLists()->getByTitle($listName);
            $items  = $list->getItems()->get()->executeQuery();

            foreach ($items as $item) {
                if ($item->getProperty('Id') === $sharePointId) {
                    $listItem = $item;
                    break;
                }
            }
        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
        }

        return $listItem;
    }
    /**
     * Pobierz liste kolumn jakie występuja na liście
     * @param string $listName
     * @return array
     */
    public function getColumnItem(string $listName):array {
        $columns = [];
        try {
            $client = $this->spApi->getClient();
            $list   = $client->getWeb()->getLists()->getByTitle($listName);
            /** @var ListItemCollection $items */
            $items  = $list->getItems()->get()->top(1)->executeQuery();
            /** @var ListItem $item  */
            
            foreach ($items as $item) {
                $columns = array_keys($item->toJson());
                break;
            }
        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
            return [];
        }

        return $columns;
    }
    /**
     * Dodaj element/item do listy
     * @param string $listName
     * @param array $data
     * @return ListItem|null
     */
    public function add(string $listName, array $data): ListItem|null {
        $item = null;
        try {

            $client = $this->spApi->getClient();
            $list   = $client->getWeb()->getLists()->getByTitle($listName);
            $item   = $list->addItem($data)->executeQuery();
        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
        }
        // $id = $item->getProperty('Id');
        return $item;
    }
    public function update(string $listName, int $sharePointId, array $properties = []): void{
        try {
            $item = $this->getItem($listName, $sharePointId);
            if ($item == null) {
                throw new SharePointException('Brak itemu');
            }
            foreach ($properties as $name => $value) {
                $item->setProperty($name, $value);
            }
            $item->update()->executeQuery();
        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
        }
    }
    /**
     * Dodaj załącznik do elementu/itemu
     * @param ListItem $listItem
     * @param string $filePath
     * @return bool
     */
    public function addAttachment(ListItem $listItem, string $filePath): bool {

        try {

            if (file_exists($filePath) == false) {
                throw new SharePointException('Dodaj załącznik - Plik nie istnieje');
            }
            // $client = $this->spApi->getClient();
            // $user = $client->getWeb()->getSiteUsers()->getByEmail('damian.samsoniuk@passwordit.pl')->get()->executeQuery();
            $listItem->getAttachmentFiles()->add($filePath);
            //update list item system metadata
            $fieldValues = array(
                // 'Editor' => FieldUserValue::fromUser($user),
                // 'Author' => FieldUserValue::fromUser($user),
            );
            $listItem->validateUpdateListItem($fieldValues)->executeQuery();
        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
            return false;
        }

        return true;
    }

}