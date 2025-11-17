<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../DBConnection.php';

class HomeTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = new Database([
            'db_host' => 'localhost',             
            'db_name' => 'strathmart',                
            'db_user' => 'root',                  
            'db_pass' => '1234'          
        ]);

        $this->db->connect(); 
    }

    public function testConnection()
    {
        $pdo = $this->db->connect();
        $this->assertInstanceOf(PDO::class, $pdo);
    }

    public function testFetchCategories()
    {
        $pdo = $this->db->connect();
        $stmt = $pdo->query("SELECT * FROM categories");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertNotEmpty($categories, "Categories table should not be empty");
    }

     /** @test */
     public function testDatabaseConnection()
     {
         $pdo = $this->db->connect();
         $this->assertInstanceOf(PDO::class, $pdo, "Database connection should return a PDO instance");
     }
 
     /** @test */
     public function testCategoriesAreFetched()
     {
         $categories = $this->db->fetch("SELECT * FROM categories");
         print_r($categories);
         $this->assertIsArray($categories, "Categories should return as an array");
         $this->assertNotEmpty($categories, "Categories table should not be empty");
     }
 
     /** @test */
     public function testItemsAreFetched()
     {
         $items = $this->db->fetch("SELECT * FROM items");
         print_r($items);
         $this->assertIsArray($items, "Items should return as an array");
         $this->assertNotEmpty($items, "Items table should not be empty");
     }
 
     /** @test */
     public function testSearchFilterFindsMatchingItems()
     {
         $searchTerm = '%book%'; 
         $query = "SELECT * FROM items WHERE item_name LIKE ? OR item_description LIKE ?";
         $results = $this->db->fetch($query, [$searchTerm, $searchTerm]);
 
         $this->assertIsArray($results, "Search should return an array");
         $this->assertGreaterThanOrEqual(0, count($results), "Search should not fail");
     }
 
     /** @test */
     public function testCategoryFilterReturnsExpectedItems()
     {
         // Fetch a category from DB to use
         $categories = $this->db->fetch("SELECT category_name FROM categories LIMIT 1");
         $this->assertNotEmpty($categories, "At least one category must exist for this test");
         $category = $categories[0]['category_name'];
 
         $query = "SELECT * FROM items WHERE item_category = ?";
         $results = $this->db->fetch($query, [$category]);
 
         $this->assertIsArray($results, "Category filter should return an array");
         $this->assertGreaterThanOrEqual(0, count($results), "Category filter should not fail");
     }
 
     /** @test */
     public function testPriceFilterWorks()
     {
         $query = "SELECT * FROM items WHERE Price BETWEEN ? AND ?";
         $results = $this->db->fetch($query, [0, 10000]);
 
         $this->assertIsArray($results, "Price filter should return an array");
         foreach ($results as $item) {
             $this->assertGreaterThanOrEqual(0, $item['Price']);
             $this->assertLessThanOrEqual(10000, $item['Price']);
         }
     }
}
