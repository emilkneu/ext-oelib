<?php

declare(strict_types=1);

namespace OliverKlee\Oelib\Mapper;

use OliverKlee\Oelib\Exception\NotFoundException;
use OliverKlee\Oelib\Model\Country;

/**
 * @extends AbstractDataMapper<Country>
 */
class CountryMapper extends AbstractDataMapper
{
    /**
     * @var non-empty-string the name of the database table for this mapper
     */
    protected $tableName = 'static_countries';

    /**
     * @var class-string<Country> the model class name for this mapper, must not be empty
     */
    protected $modelClassName = Country::class;

    /**
     * @var array<int, string> the column names of additional string keys
     */
    protected $additionalKeys = ['cn_iso_2', 'cn_iso_3'];

    /**
     * Finds a country by its ISO 3166-1 alpha-2 code.
     *
     * @param non-empty-string $isoAlpha2Code the ISO 3166-1 alpha-2 code to find
     *
     * @return Country the country
     *
     * @throws NotFoundException if there is no record with the provided ISO 3166-1 alpha-2 code
     */
    public function findByIsoAlpha2Code(string $isoAlpha2Code): Country
    {
        /** @var Country $result */
        $result = $this->findOneByKey('cn_iso_2', $isoAlpha2Code);

        return $result;
    }

    /**
     * Finds a country by its ISO 3166-1 alpha-3 code.
     *
     * @param non-empty-string $isoAlpha3Code the ISO 3166-1 alpha-3 code to find
     *
     * @return Country the country
     *
     * @throws NotFoundException if there is no record with the provided ISO 3166-1 alpha-3 code
     */
    public function findByIsoAlpha3Code(string $isoAlpha3Code): Country
    {
        /** @var Country $result */
        $result = $this->findOneByKey('cn_iso_3', $isoAlpha3Code);

        return $result;
    }
}
