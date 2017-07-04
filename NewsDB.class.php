<?php
require_once "inc/lib.inc.php";

class NewsDB implements INewsDB{
    const DB_NAME = "news.db";
    const RSS_NAME = "newsRss.xml";
    const RSS_TITLE = "Последние новости";
    const RSS_LINK = "news.php";
    private $_db;

    function __construct(){
        $this->_db = new SQLite3(self::DB_NAME);

        if(filesize(self::DB_NAME) == 0){
            $tableMsgs = "CREATE TABLE msgs(id INTEGER PRIMARY KEY AUTOINCREMENT,
                                            title TEXT,
                                            category INTEGER,
                                            description TEXT,
                                            source TEXT,
                                            datetime INTEGER
                                        )";
            $tableCats = "CREATE TABLE category(id INTEGER, name TEXT)";
            $insertCats = "INSERT INTO category(id, name)
                            SELECT 1 as id, 'Политика' as name
                            UNION SELECT 2 as id, 'Культура' as name
                            UNION SELECT 3 as id, 'Спорт' as name"; 
            if(!$this->_db->exec($tableMsgs))
                throw new Exception('Table msgs not created');                           
            if(!$this->_db->exec($tableCats))
                throw new Exception('Table category not created');                           
            if(!$this->_db->exec($insertCats))
                throw new Exception('Table catigory not inserted');                           
        }
    }

    function __destruct(){
        unset($this->_db);
    }

    function __get($name) {
        if($name == "db") return $this->_db;

        throw new Exception("Invalid property name");
    }

    private function createRss(){
        $dom = new DOMDocument("1.0", "utf-8");
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        $rss = $dom->createElement('rss');
        $version = $dom->createAttribute("version");
        $version->value = '2.0';
        $rss->appendChild($version);

        $dom->appendChild($rss);

        $channel = $dom->createElement('channel');
        $rss->appendChild($channel);

        $title = $dom->createElement('title', self::RSS_TITLE);
        $link = $dom->createElement('link', self::RSS_LINK);
        $channel->appendChild($title);
        $channel->appendChild($link);
        
        $newsArr = $this->getNews();

        foreach ($newsArr as $value) {
            $item = $dom->createElement('item');

            $itemTitle = $dom->createElement('title', $value['title']);
            $itemLink = $dom->createElement('link', $value['source']);

            $itemDescription = $dom->createElement('title');
            $descriptionCdata = $dom->createCDATASection($value['description']);
            $itemDescription->appendChild($descriptionCdata);
            
            $dt = date("r", $value['datetime']);
            $itemPubdate = $dom->createElement('pubdate', $dt);
            $itemCategory = $dom->createElement('category', $value['category']);

            $item->appendChild($itemTitle);
            $item->appendChild($itemLink);
            $item->appendChild($itemDescription);
            $item->appendChild($itemPubdate);
            $item->appendChild($itemCategory);

            $channel->appendChild($item);
        }

        $dom->save(self::RSS_NAME);

    }

    function saveNews($title, $category, $description, $source){
        $dt = time();
        $sql = "INSERT INTO msgs (title, category, description, source, datetime)
                VALUES ('$title', $category, '$description', '$source', $dt)";
        
        $res = $this->_db->exec($sql);
        if(!$res) return false;

        $this->createRss();

        return $res;
    }

    private function db2Arr($data) {
        $arr = [];

        while($row = $data->fetchArray(SQLITE3_ASSOC))
            $arr[] = $row;
        return $arr;
    }

    function getNews(){
        $sql = "SELECT msgs.id as id, title, category.name as category,
                    description, source, datetime
                FROM msgs, category
                WHERE category.id = msgs.category
                ORDER BY msgs.id DESC";

        if(!$result = $this->_db->query($sql))
            throw new Exception('ERROE with select from DB');

        return self::db2Arr($result);
    }

    function deleteNews($id){
        $sql = "DELETE FROM msgs WHERE id = $id";

        return $this->_db->exec($sql);
    }

    function clearStr($data) {
        return strip_tags($this->_db->escapeString($data));
    }

    function clearInt($data) {
        return abs((int)($data));
    }
}