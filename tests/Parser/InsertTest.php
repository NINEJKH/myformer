<?php

namespace AfriCC\Tests\EPP;

use MyFormer\Parser\Insert;
use PHPUnit\Framework\TestCase;

class InsertTest extends TestCase
{
    public function testJsonText()
    {
        $text = <<<'EOD'
{\n    \"title\": \"just a test!\",\n    \"description\": \"foobaaarb\",\n    \"destination\": {\n        \"branch\": {\n            \"name\": \"ma\\\"ster\"\n        }\n    }\n}
EOD;
        //var_dump($text);
        $sql = <<<EOD
INSERT INTO `test` (`id`, `name`, `email`, `text`, `created_at`) VALUES (2,'bar foo','barfoo@bar.foo','{$text}','2018-01-24 13:14:18');
EOD;

        $insert = new Insert($sql);

        $this->assertEquals('test', $insert->table);
        $this->assertEquals([
            'id', 'name', 'email', 'text', 'created_at'
        ], $insert->columns);

        $this->assertEquals([
            '2', '\'bar foo\'', '\'barfoo@bar.foo\'', "'{$text}'", '\'2018-01-24 13:14:18\''
        ], $insert->values[0]);
    }

    public function testSingleQuoteText()
    {
        $text = <<<'EOD'
\"\'
EOD;
        //var_dump($text);
        $sql = <<<EOD
INSERT INTO `test` (`id`, `name`, `email`, `text`, `created_at`) VALUES (4,'jun jun','jun@jun.co.za','{$text}','2018-01-24 13:16:11');
EOD;

        $insert = new Insert($sql);

        $this->assertEquals('test', $insert->table);
        $this->assertEquals([
            'id', 'name', 'email', 'text', 'created_at'
        ], $insert->columns);

        $this->assertEquals([
            '4', '\'jun jun\'', '\'jun@jun.co.za\'', "'{$text}'", '\'2018-01-24 13:16:11\''
        ], $insert->values[0]);
    }

    public function testExtendedInsert()
    {
        $sql = <<<'EOD'
INSERT INTO `test` (`id`, `name`, `email`, `text`, `created_at`) VALUES (1,'foo bar','foobar@foo.bar',NULL,'2018-01-24 13:13:30'),(2,'bar foo','barfoo@bar.foo','{\n    \"title\": \"just a test!\",\n    \"description\": \"foobaaarb\",\n    \"destination\": {\n        \"branch\": {\n            \"name\": \"ma\\\"ster\"\n        }\n    }\n}','2018-01-24 13:14:18'),(3,'g g','gg@gg.de','\\\\\\\\','2018-01-24 13:16:03'),(4,'jun(a),(b); jun','jun@jun.co.za','\"\'','2018-01-24 13:16:11');
EOD;

        $expected_3 = <<<'EOD'
\\\\\\\\
EOD;
        $expected_4 = <<<'EOD'
\"\'
EOD;

        $insert = new Insert($sql);

        $this->assertEquals('test', $insert->table);
        $this->assertEquals([
            'id', 'name', 'email', 'text', 'created_at'
        ], $insert->columns);

        $this->assertEquals([
            ["1", "'foo bar'", "'foobar@foo.bar'", "NULL", "'2018-01-24 13:13:30'"],
            ["2", "'bar foo'", "'barfoo@bar.foo'", '\'{\n    \"title\": \"just a test!\",\n    \"description\": \"foobaaarb\",\n    \"destination\": {\n        \"branch\": {\n            \"name\": \"ma\\\\\"ster\"\n        }\n    }\n}\'', "'2018-01-24 13:14:18'"],
            ["3", "'g g'", "'gg@gg.de'", "'{$expected_3}'", "'2018-01-24 13:16:03'"],
            ["4", "'jun(a),(b); jun'", "'jun@jun.co.za'", "'{$expected_4}'", "'2018-01-24 13:16:11'"],
        ], $insert->values);
    }
}
