<?php

namespace app\components\SharePoint;

use Office365\OneDrive\ListItems\ListItemCollection;
use Office365\SharePoint\SPList;

/**
 * SharePoint Listy (np. Dokumenty, Galeria motywów, Zdarzenia, Szablony formularzy)
 */
class SharePointList {
    public $spApi;
    /**
     * @param InterfaceSharePoint $spApi
     */
    public function __construct(InterfaceSharePoint $spApi) {
        $this->spApi = $spApi;
    }
    /**
     * Pobierz listy
     */
    public function getLists(): array{

        $data = [];
        try {

            $client = $this->spApi->getClient();
            $lists  = $client->getWeb()->getLists()->get()->executeQuery();
            /** @var SPList $list */
            foreach ($lists as $list) {
                // $data[] = $list->getId();
                $data[] = $list->getTitle();
            }

        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
            return [];
        }

        return $data;
    }
    public function getId(string $listName): int|null{

        $id = null;
        try {
            $client = $this->spApi->getClient();
            $list   = $client->getWeb()->getLists()->getByTitle($listName);
            /** @var ListItemCollection $items */
            $items  = $list->getItems()->get()->top(1)->executeQuery();
            if($items->getCount() !== 1){
                throw new SharePointException('Nie znaleziono listy '. $listName);
            }
            $id = $items[0]->getProperty("Id");
        } catch (\Exception $e) {
            $this->spApi->addError($e->getMessage());
        }
        return $id;
    }
    
}