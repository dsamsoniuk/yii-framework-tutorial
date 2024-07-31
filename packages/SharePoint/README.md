# SharePoint API

Microsoft SharePoint – platforma oprogramowania do pracy grupowej firmy Microsoft w formie aplikacji webowej. Jest zaprojektowana z myślą o złożonych aplikacjach webowych oraz wspiera rozmaite kombinacje dotyczące zarządzania, publikacji oraz manipulacji informacjami pomiędzy użytkownikami w sieci korporacyjnej. 

Użyta paczka: https://github.com/vgrem/phpSPO
Przykłady: https://github.com/vgrem/phpSPO/tree/master/examples


## Ogolna kolejnosc

site webAPI > List (Lista list) > listItems/Item 
site webAPI > Shared documents

service_list_item_sp
    - item_sp_id                -- id zadania w SharePoint
    - service_id                -- zlecenie
    - service_list_item_file    -- lista plikow
        - project_file_id       -- relacja do pliku projektowego

## Konfiguracja

W katalogu /config/api.php dodajemy 2 parametry ('apiSharePointClientID' i 'apiSharePointClientSecret')

```php
    'apiSharePointClientID' => 'xxxxxxx',
    'apiSharePointClientSecret' => 'xxxxxx',
    'apiSharePointMovieDir' => 'Shared Documents/technFilmy', // zmieniamy sciezke mediow dla podpisanych zlecen
    'apiSharePointSite' => 'https://passworditpl.sharepoint.com/sites/WebAPI', // 
    'apiSharePointListTaskName' => 'issue_test', // nazwa listy w ktorej beda dodawane zadania
```

Jak wygenerowac dane dostepu: https://learn.microsoft.com/en-us/sharepoint/dev/solution-guidance/security-apponly-azureacs


### Na potrzeby testów można korzystać z Testowej klasy

```php
// Testowa
(new SharePointList(new SharePointTestSite()))

// Produkcyjna
(new SharePointList(new SharePointWebAPI()))
```

### Pobieranie listy list

```php
    $list = (new SharePointList(new SharePointWebAPI()))->getLists();
```

###  elementy/itemy 

```php
    // issue_test - jest przykladem nazwy listy ktora zawiera tabele z zadaniami

    (new SharePointListItems(new SharePointWebAPI()))->add('issue_test', [
        'Title' => "New task N#" . rand(1, 100000),
    ]);
    // Lista kolumn w liscie np. issue_test
    $list = (new SharePointListItems(new SharePointWebAPI()))->getColumnItem('issue_test');
    // Lista zadan 
    $list = (new SharePointListItems(new SharePointWebAPI()))->getItems('issue_test');

    // Edycja zadania z listy issue_test
    (new SharePointListItems(new SharePointWebAPI()))->update('issue_test', 3, [
        'Etapzadania' => 'Wykonane'
    ]);

```

### Dodawanie katalogu

```php
    // domyslnie w Shared Documents/
    (new SharePointFolder(new SharePointWebAPI()))->create('test_dir');
    (new SharePointFolder(new SharePointWebAPI()))->create('sub_dir', 'Shared Documents/test_dir');
    (new SharePointFolder(new SharePointWebAPI()))->get('Jakis_katalog); // jak nie ma to null
```

### Wysyłanie pliku do wybranego katalogu (domyslnie do "Shared documents/")

```php
    (new SharePointFile(new SharePointWebAPI()))->upload('/var/www/html/test.txt', '__katalog__sciezka_w_SP');
```
