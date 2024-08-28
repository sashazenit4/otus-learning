<?php

namespace Otus\Models;

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\SystemException;
use CIBlockElement;

/**
 * Class AbstractIblockPropertyValueTable
 *
 * @package Models
 */
abstract class AbstractIblockPropertyValuesTable extends DataManager
{
    const IBLOCK_ID = null;

    protected static ?array $properties = null;
    protected static ?CIBlockElement $iblockElement = null;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'b_iblock_element_prop_s'.static::IBLOCK_ID;
    }

    /**
     * @return string
     */
    public static function getTableNameMulti(): string
    {
        return 'b_iblock_element_prop_m'.static::IBLOCK_ID;
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getMap(): array
    {
        $cache = Cache::createInstance();
        $cacheDir = 'iblock_property_map/'.static::IBLOCK_ID;
        $multipleValuesTableClass = static::getMultipleValuesTableClass();

        if ($cache->initCache(0, md5($cacheDir), $cacheDir)) {
            $map = $cache->getVars();

        } else {
            $cache->startDataCache();

            $map['IBLOCK_ELEMENT_ID'] = new IntegerField('IBLOCK_ELEMENT_ID', ['primary' => true]);
            $map['ELEMENT'] = new ReferenceField(
                'ELEMENT',
                ElementTable::class,
                ['=this.IBLOCK_ELEMENT_ID' => 'ref.ID']
            );

            foreach (static::getProperties() as $property) {
                if ($property['MULTIPLE'] === 'Y') {
                    $map[$property['CODE']] = new ExpressionField(
                        $property['CODE'],
                        sprintf('(select group_concat(`VALUE` SEPARATOR "\0") as VALUE from %s as m where m.IBLOCK_ELEMENT_ID = %s and m.IBLOCK_PROPERTY_ID = %d)',
                            static::getTableNameMulti(),
                            '%s',
                            $property['ID']
                        ),
                        ['IBLOCK_ELEMENT_ID'],
                        ['fetch_data_modification' => [static::class, 'getMultipleFieldValueModifier']]
                    );

                    if ($property['USER_TYPE'] === 'EList' || $property['PROPERTY_TYPE'] === 'E') {
                        $map[$property['CODE'].'_ELEMENT_NAME'] = new ExpressionField(
                            $property['CODE'].'_ELEMENT_NAME',
                            sprintf('(select group_concat(e.NAME SEPARATOR "\0") as VALUE from %s as m join b_iblock_element as e on m.VALUE = e.ID where m.IBLOCK_ELEMENT_ID = %s and m.IBLOCK_PROPERTY_ID = %d)',
                                static::getTableNameMulti(),
                                '%s',
                                $property['ID']
                            ),
                            ['IBLOCK_ELEMENT_ID'],
                            ['fetch_data_modification' => [static::class, 'getMultipleFieldValueModifier']]
                        );
                    }

                    $map[$property['CODE'].'|SINGLE'] = new ReferenceField(
                        $property['CODE'].'|SINGLE',
                        $multipleValuesTableClass,
                        [
                            '=this.IBLOCK_ELEMENT_ID' => 'ref.IBLOCK_ELEMENT_ID',
                            '=ref.IBLOCK_PROPERTY_ID' => new SqlExpression('?i', $property['ID'])
                        ]
                    );

                    continue;
                }

                if ($property['PROPERTY_TYPE'] == PropertyTable::TYPE_NUMBER) {
                    $map[$property['CODE']] = new IntegerField("PROPERTY_{$property['ID']}");
                } elseif ($property['USER_TYPE'] === 'Date') {
                    $map[$property['CODE']] = new DatetimeField("PROPERTY_{$property['ID']}");
                } else {
                    $map[$property['CODE']] = new StringField("PROPERTY_{$property['ID']}");
                }

                if ($property['PROPERTY_TYPE'] === 'E' && ($property['USER_TYPE'] === 'EList' || is_null($property['USER_TYPE']))) {
                    $map[$property['CODE'].'_ELEMENT'] = new ReferenceField(
                        $property['CODE'].'_ELEMENT',
                        ElementTable::class,
                        ["=this.{$property['CODE']}" => 'ref.ID']
                    );
                }
            }

            if (empty($map)) {
                $cache->abortDataCache();
            } else {
                $cache->endDataCache($map);
            }
        }

        return $map;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public static function add(array $data)
    {
            static::$iblockElement ?? static::$iblockElement = new CIBlockElement();
        $fields = [
            'NAME'            => $data['NAME'],
            'IBLOCK_ID'       => static::IBLOCK_ID,
            'PROPERTY_VALUES' => $data,
        ];

        $elementId = static::$iblockElement->Add($fields);
        if ($elementId) {
            return $elementId;
        } else {
            // Логирование или обработка ошибки
            $error = static::$iblockElement->LAST_ERROR;
            // Вы можете обработать ошибку или выбросить исключение
            throw new \Exception("Error adding element: " . $error);
        }
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public static function update($id, array $data): bool
    {
            static::$iblockElement ?? static::$iblockElement = new CIBlockElement();
        $fields = [
            'IBLOCK_ID'       => static::IBLOCK_ID,
        ];

        $fields = array_merge($fields, $data);

        return static::$iblockElement->Update($id, $fields);
    }

    /**
     * @param $primary
     *
     * @return DeleteResult
     * @throws NotImplementedException
     */
    public static function delete($primary): void
    {
            static::$iblockElement ?? static::$iblockElement = new CIBlockElement();

        static::$iblockElement->Delete($primary);
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public static function getProperties(): array
    {
        if (isset(static::$properties[static::IBLOCK_ID])) {
            return static::$properties[static::IBLOCK_ID];
        }

        $dbResult = PropertyTable::query()
            ->setSelect(['ID', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'NAME', 'USER_TYPE'])
            ->where('IBLOCK_ID', static::IBLOCK_ID)
            ->exec();
        while ($row = $dbResult->fetch()) {
            static::$properties[static::IBLOCK_ID][$row['CODE']] = $row;
        }

        return static::$properties[static::IBLOCK_ID] ?? [];
    }

    /**
     * @param  string  $code
     *
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getPropertyId(string $code): int
    {
        return (int) static::getProperties()[$code]['ID'];
    }

    /**
     * @return array
     */
    public static function getMultipleFieldValueModifier(): array
    {
        return [fn ($value) => array_filter(explode("\0", $value))];
    }

    /**
     * @return array
     */
    public static function getMultipleFieldIdValueModifier(): array
    {
        return [function ($value) {
            $result = [];
            $values = array_filter(explode("\0", $value));
            foreach ($values as $val) {
                list($id, $name) = explode(';', $val);
                $result[$id] = $name;
            }
            return $result;
        }];
    }

    /**
     * @param  int|null  $iblockId
     */
    public static function clearPropertyMapCache(?int $iblockId = null): void
    {
        $iblockId = $iblockId ?: static::IBLOCK_ID;
        if (empty($iblockId)) {
            return;
        }

        Cache::clearCache(true, "iblock_property_map/$iblockId");
    }

    /**
     * @param  string  $propertyCode
     * @param  string  $byKey
     *
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getEnumPropertyOptions(string $propertyCode, string $byKey = 'ID'): array
    {
        $dbResult = PropertyEnumerationTable::getList([
            'select' => ['ID', 'VALUE', 'XML_ID', 'SORT'],
            'filter' => ['=PROPERTY.CODE' => $propertyCode, 'PROPERTY.IBLOCK_ID' => static::IBLOCK_ID],
        ]);
        while ($row = $dbResult->fetch()) {
            $enumPropertyOptions[$row[$byKey]] = $row;
        }

        return $enumPropertyOptions ?? [];
    }

    /**
     * @return string
     */
    private static function getMultipleValuesTableClass(): string
    {
        $partsOfClassName = explode('\\', static::class);
        $className = end($partsOfClassName);
        $namespace = str_replace('\\'.$className, '', static::class);
        $className = str_replace('Table', 'MultipleTable', $className);

        return $namespace.'\\'.$className;
    }

}