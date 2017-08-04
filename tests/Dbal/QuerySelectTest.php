<?php

namespace Runn\tests\Dbal\Query;

use Runn\Dbal\Query;

class QuerySelectTest extends \PHPUnit_Framework_TestCase
{

    protected function getExpectationsWithoutEmptyPrepareNames()
    {
        return [
            [
                'args' => ['foo'],
                'result' => ['foo']
            ],
            [
                'args' => ['`foo`'],
                'result' => ['foo']
            ],
            [
                'args' => ['`foo` AS `bar`'],
                'result' => ['`foo` AS `bar`']
            ],
            [
                'args' => ['foo1, bar1'],
                'result' => ['foo1', 'bar1']
            ],
            [
                'args' => ['foo2', 'bar2'],
                'result' => ['foo2', 'bar2']
            ],
            [
                'args' => ['foo3, `bar3`'],
                'result' => ['foo3', 'bar3']
            ],
            [
                'args' => ['foo4', '"bar4"'],
                'result' => ['foo4', 'bar4']
            ],
            [
                'args' => ['foo.bar'],
                'result' => ['foo.bar']
            ],
            [
                'args' => ['"foo"."bar"'],
                'result' => ['foo.bar']
            ],
        ];
    }

    protected function getExpectationsWithoutEmptyWithoutTrimPrepareNames()
    {
        return [
            [
                'args' => ['foo'],
                'result' => ['foo']
            ],
            [
                'args' => ['`foo`'],
                'result' => ['`foo`']
            ],
            [
                'args' => ['`foo` AS `bar`'],
                'result' => ['`foo` AS `bar`']
            ],
            [
                'args' => ['foo1, bar1'],
                'result' => ['foo1', 'bar1']
            ],
            [
                'args' => ['foo2', 'bar2'],
                'result' => ['foo2', 'bar2']
            ],
            [
                'args' => ['foo3, `bar3`'],
                'result' => ['foo3', '`bar3`']
            ],
            [
                'args' => ['foo4', '"bar4"'],
                'result' => ['foo4', '"bar4"']
            ],
            [
                'args' => ['foo.bar'],
                'result' => ['foo.bar']
            ],
            [
                'args' => ['"foo"."bar"'],
                'result' => ['"foo"."bar"']
            ],
        ];
    }

    protected function getExpectationsForPrepareNames()
    {
        return
            $this->getExpectationsWithoutEmptyPrepareNames() +
            [
                [
                    'args' => [],
                    'result' => []
                ],
            ];
    }

    protected function getExpectationsForColumnsPrepareNames()
    {
        $expectations =
            $this->getExpectationsWithoutEmptyPrepareNames() + [
                [
                    'args' => [],
                    'result' => ['*']
                ],
                [
                    'args' => ['*'],
                    'result' => ['*']
                ],
                [
                    'args' => ['foo, bar, *'],
                    'result' => ['foo', 'bar']
                ],
            ];
        return $expectations;
    }

    public function testColumns()
    {
        $expectations = $this->getExpectationsForColumnsPrepareNames();

        foreach ($expectations as $exp) {

            $query = new Query();
            $q = $query->columns(...$exp['args']);

            $this->assertInstanceOf(Query::class, $q);
            $this->assertSame($q, $query);
            $this->assertSame($exp['result'], $query->columns);

        }
    }

    public function testSelectColumns()
    {
        $expectations = $this->getExpectationsForColumnsPrepareNames();

        foreach ($expectations as $exp) {

            $query = new Query();
            $q = $query->select(...$exp['args']);

            $this->assertInstanceOf(Query::class, $q);
            $this->assertSame($q, $query);
            $this->assertSame('select', $query->action);
            $this->assertSame($exp['result'], $query->columns);

        }
    }

    public function testColumn()
    {

        $query = new Query();
        $q = $query->column();

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['*'], $query->columns);

        $query = new Query();
        $q = $query->column('*');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['*'], $query->columns);

        $query = new Query();

        $q = $query->column('foo');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['foo'], $query->columns);

        $q = $q->column('`bar`');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['foo', 'bar'], $query->columns);
    }

    public function testFromAndTables()
    {
        $expectations = $this->getExpectationsForPrepareNames();

        foreach ($expectations as $exp) {

            $query = new Query();
            $q = $query->from(...$exp['args']);

            $this->assertInstanceOf(Query::class, $q);
            $this->assertSame($q, $query);
            $this->assertSame($exp['result'], $query->tables);

            $query = new Query();
            $q = $query->tables(...$exp['args']);

            $this->assertInstanceOf(Query::class, $q);
            $this->assertSame($q, $query);
            $this->assertSame($exp['result'], $query->tables);

        }
    }

    public function testTable()
    {
        $query = new Query();

        $q = $query->table('foo');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['foo'], $query->tables);

        $q = $query->table('bar');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['foo', 'bar'], $query->tables);

        $q = $query->table('`baz`');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['foo', 'bar', 'baz'], $query->tables);
    }

    public function testWith()
    {
        $expectations = $this->getExpectationsForPrepareNames();

        $query = new Query();

        foreach ($expectations as $exp) {

            $q = $query->with(...$exp['args'])->select();

            $this->assertInstanceOf(Query::class, $q);
            $this->assertSame($q, $query);
            $this->assertSame('select', $query->action);
            $this->assertSame(['*'], $query->columns);
            $this->assertSame($exp['result'], $query->with);

        }
    }

    public function testJoins()
    {
        $query = new Query();
        $query->select()->from('foo');

        $q = $query->join('bar', 'bar.id=foo.bar_id');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('select', $query->action);
        $this->assertSame(['foo'], $query->tables);
        $this->assertSame([
            ['table' => 'bar', 'on' => 'bar.id=foo.bar_id', 'type' => 'full'],
        ], $query->joins);

        $q = $query->join('baz', 'baz.id=foo.baz_id', 'left');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('select', $query->action);
        $this->assertSame(['foo'], $query->tables);
        $this->assertSame([
            ['table' => 'bar', 'on' => 'bar.id=foo.bar_id', 'type' => 'full'],
            ['table' => 'baz', 'on' => 'baz.id=foo.baz_id', 'type' => 'left'],
        ], $query->joins);

        $q = $query->join('bla', 'bla.id=foo.bla_id', 'right', 'b');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('select', $query->action);
        $this->assertSame(['foo'], $query->tables);
        $this->assertSame([
            ['table' => 'bar', 'on' => 'bar.id=foo.bar_id', 'type' => 'full'],
            ['table' => 'baz', 'on' => 'baz.id=foo.baz_id', 'type' => 'left'],
            ['table' => 'bla', 'on' => 'bla.id=foo.bla_id', 'type' => 'right', 'alias' => 'b'],
        ], $query->joins);

        $q = $query->joins([
            ['table' => 'baz', 'on' => 'baz.id=foo.baz_id', 'type' => 'left'],
            ['table' => 'bla', 'on' => 'bla.id=foo.bla_id', 'type' => 'right', 'alias' => 'b'],
        ]);

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('select', $query->action);
        $this->assertSame(['foo'], $query->tables);
        $this->assertSame([
            ['table' => 'baz', 'on' => 'baz.id=foo.baz_id', 'type' => 'left'],
            ['table' => 'bla', 'on' => 'bla.id=foo.bla_id', 'type' => 'right', 'alias' => 'b'],
        ], $query->joins);
    }

    public function testWhere()
    {
        $query = new Query();

        $q = $query->select()->from('foo')->where('foo.id=1');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('select', $query->action);
        $this->assertSame(['*'], $query->columns);
        $this->assertSame(['foo'], $query->tables);
        $this->assertSame('foo.id=1', $query->where);
    }

    public function testGroup()
    {
        $expectations = $this->getExpectationsWithoutEmptyWithoutTrimPrepareNames();

        $query = new Query();

        foreach ($expectations as $exp) {

            $q = $query->select()->from('foo')->group(...$exp['args'])->select();

            $this->assertInstanceOf(Query::class, $q);
            $this->assertSame($q, $query);
            $this->assertSame('select', $query->action);
            $this->assertSame(['*'], $query->columns);
            $this->assertSame(['foo'], $query->tables);
            $this->assertSame($exp['result'], $query->group);

        }
    }

    public function testHaving()
    {
        $query = new Query();

        $q = $query->select()->from('foo')->group('id')->having('name=:name');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('select', $query->action);
        $this->assertSame(['*'], $query->columns);
        $this->assertSame(['foo'], $query->tables);
        $this->assertSame(['id'], $query->group);
        $this->assertSame('name=:name', $query->having);
    }

    public function testOrder()
    {

        $expectations = $this->getExpectationsWithoutEmptyWithoutTrimPrepareNames();

        $query = new Query();

        foreach ($expectations as $exp) {

            $q = $query->select()->from('foo')->order(...$exp['args']);

            $this->assertInstanceOf(Query::class, $q);
            $this->assertSame($q, $query);
            $this->assertSame('select', $query->action);
            $this->assertSame(['*'], $query->columns);
            $this->assertSame(['foo'], $query->tables);
            $this->assertSame($exp['result'], $query->order);

        }
    }

    public function testOffsetLimit()
    {
        $query = new Query();

        $q = $query->select()->from('foo')->offset(10);

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('select', $query->action);
        $this->assertSame(['*'], $query->columns);
        $this->assertSame(['foo'], $query->tables);
        $this->assertSame(10, $query->offset);

        $q = $query->select()->from('foo')->limit(20);

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('select', $query->action);
        $this->assertSame(['*'], $query->columns);
        $this->assertSame(['foo'], $query->tables);
        $this->assertSame(20, $query->limit);
    }

}