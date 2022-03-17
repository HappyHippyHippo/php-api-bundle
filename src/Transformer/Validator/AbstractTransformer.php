<?php

namespace Hippy\Api\Transformer\Validator;

use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;

abstract class AbstractTransformer implements TransformerInterface
{
    /** @var array<string, int> */
    public const VIOLATION_TO_ERROR_CODE = [
        Assert\AtLeastOneOf::AT_LEAST_ONE_OF_ERROR => 1,
        Assert\Bic::INVALID_CHARACTERS_ERROR => 10,
        Assert\Bic::INVALID_BANK_CODE_ERROR => 11,
        Assert\Bic::INVALID_CASE_ERROR => 12,
        Assert\Bic::INVALID_COUNTRY_CODE_ERROR => 13,
        Assert\Bic::INVALID_IBAN_COUNTRY_CODE_ERROR => 14,
        Assert\Bic::INVALID_LENGTH_ERROR => 15,
        Assert\Blank::NOT_BLANK_ERROR => 20,
        Assert\Collection::MISSING_FIELD_ERROR => 30,
        Assert\Collection::NO_SUCH_FIELD_ERROR => 31,
        Assert\CardScheme::INVALID_FORMAT_ERROR => 40,
        Assert\CardScheme::NOT_NUMERIC_ERROR => 41,
        Assert\Choice::NO_SUCH_CHOICE_ERROR => 50,
        Assert\Choice::TOO_FEW_ERROR => 51,
        Assert\Choice::TOO_MANY_ERROR => 52,
        Assert\Count::TOO_MANY_ERROR => 60,
        Assert\Count::TOO_FEW_ERROR => 61,
        Assert\Country::NO_SUCH_COUNTRY_ERROR => 70,
        Assert\Currency::NO_SUCH_CURRENCY_ERROR => 80,
        Assert\Date::INVALID_FORMAT_ERROR => 90,
        Assert\Date::INVALID_DATE_ERROR => 91,
        Assert\DateTime::INVALID_DATE_ERROR => 100,
        Assert\DateTime::INVALID_FORMAT_ERROR => 101,
        Assert\DateTime::INVALID_TIME_ERROR => 102,
        Assert\DivisibleBy::NOT_DIVISIBLE_BY => 110,
        Assert\Email::INVALID_FORMAT_ERROR => 120,
        Assert\EqualTo::NOT_EQUAL_ERROR => 130,
        Assert\Expression::EXPRESSION_FAILED_ERROR => 140,
        Assert\ExpressionLanguageSyntax::EXPRESSION_LANGUAGE_SYNTAX_ERROR => 150,
        Assert\File::EMPTY_ERROR => 160,
        Assert\File::INVALID_MIME_TYPE_ERROR => 161,
        Assert\File::NOT_FOUND_ERROR => 162,
        Assert\File::NOT_READABLE_ERROR => 163,
        Assert\File::TOO_LARGE_ERROR => 164,
        Assert\GreaterThan::TOO_LOW_ERROR => 170,
        Assert\GreaterThanOrEqual::TOO_LOW_ERROR => 180,
        Assert\Hostname::INVALID_HOSTNAME_ERROR => 190,
        Assert\Iban::INVALID_FORMAT_ERROR => 200,
        Assert\Iban::INVALID_COUNTRY_CODE_ERROR => 201,
        Assert\Iban::INVALID_CHARACTERS_ERROR => 202,
        Assert\Iban::CHECKSUM_FAILED_ERROR => 203,
        Assert\Iban::NOT_SUPPORTED_COUNTRY_CODE_ERROR => 204,
        Assert\IdenticalTo::NOT_IDENTICAL_ERROR => 210,
        Assert\Image::TOO_LOW_ERROR => 220,
        Assert\Image::CORRUPTED_IMAGE_ERROR => 221,
        Assert\Image::LANDSCAPE_NOT_ALLOWED_ERROR => 222,
        Assert\Image::PORTRAIT_NOT_ALLOWED_ERROR => 223,
        Assert\Image::RATIO_TOO_BIG_ERROR => 224,
        Assert\Image::RATIO_TOO_SMALL_ERROR => 225,
        Assert\Image::SIZE_NOT_DETECTED_ERROR => 226,
        Assert\Image::SQUARE_NOT_ALLOWED_ERROR => 227,
        Assert\Image::TOO_FEW_PIXEL_ERROR => 228,
        Assert\Image::TOO_HIGH_ERROR => 229,
        Assert\Image::TOO_MANY_PIXEL_ERROR => 230,
        Assert\Image::TOO_NARROW_ERROR => 231,
        Assert\Image::TOO_WIDE_ERROR => 232,
        Assert\Ip::INVALID_IP_ERROR => 240,
        Assert\Isbn::CHECKSUM_FAILED_ERROR => 250,
        Assert\Isbn::INVALID_CHARACTERS_ERROR => 251,
        Assert\Isbn::TOO_LONG_ERROR => 252,
        Assert\Isbn::TOO_SHORT_ERROR => 253,
        Assert\Isbn::TYPE_NOT_RECOGNIZED_ERROR => 254,
        Assert\IsFalse::NOT_FALSE_ERROR => 260,
        Assert\IsNull::NOT_NULL_ERROR => 270,
        Assert\Issn::TOO_SHORT_ERROR => 280,
        Assert\Issn::TOO_LONG_ERROR => 281,
        Assert\Issn::INVALID_CHARACTERS_ERROR => 282,
        Assert\Issn::CHECKSUM_FAILED_ERROR => 283,
        Assert\Issn::INVALID_CASE_ERROR => 284,
        Assert\Issn::MISSING_HYPHEN_ERROR => 285,
        Assert\IsTrue::NOT_TRUE_ERROR => 290,
        Assert\Json::INVALID_JSON_ERROR => 300,
        Assert\Language::NO_SUCH_LANGUAGE_ERROR => 310,
        Assert\Length::INVALID_CHARACTERS_ERROR => 320,
        Assert\Length::TOO_LONG_ERROR => 321,
        Assert\Length::TOO_SHORT_ERROR => 322,
        Assert\LessThan::TOO_HIGH_ERROR => 330,
        Assert\LessThanOrEqual::TOO_HIGH_ERROR => 340,
        Assert\Locale::NO_SUCH_LOCALE_ERROR => 350,
        Assert\Luhn::INVALID_CHARACTERS_ERROR => 360,
        Assert\Luhn::CHECKSUM_FAILED_ERROR => 361,
        Assert\NotBlank::IS_BLANK_ERROR => 370,
        Assert\NotCompromisedPassword::COMPROMISED_PASSWORD_ERROR => 380,
        Assert\NotEqualTo::IS_EQUAL_ERROR => 390,
        Assert\NotIdenticalTo::IS_IDENTICAL_ERROR => 400,
        Assert\NotNull::IS_NULL_ERROR => 410,
        Assert\Range::TOO_HIGH_ERROR => 420,
        Assert\Range::INVALID_CHARACTERS_ERROR => 421,
        Assert\Range::TOO_LOW_ERROR => 422,
        Assert\Range::NOT_IN_RANGE_ERROR => 423,
        Assert\Regex::REGEX_FAILED_ERROR => 430,
        Assert\Time::INVALID_FORMAT_ERROR => 440,
        Assert\Time::INVALID_TIME_ERROR => 441,
        Assert\Timezone::TIMEZONE_IDENTIFIER_ERROR => 450,
        Assert\Timezone::TIMEZONE_IDENTIFIER_IN_COUNTRY_ERROR => 451,
        Assert\Timezone::TIMEZONE_IDENTIFIER_IN_ZONE_ERROR => 452,
        Assert\Timezone::TIMEZONE_IDENTIFIER_INTL_ERROR => 453,
        Assert\Type::INVALID_TYPE_ERROR => 460,
        Assert\Unique::IS_NOT_UNIQUE => 470,
        Assert\Url::INVALID_URL_ERROR => 480,
        Assert\Uuid::INVALID_CHARACTERS_ERROR => 490,
        Assert\Uuid::TOO_SHORT_ERROR => 491,
        Assert\Uuid::TOO_LONG_ERROR => 492,
        Assert\Uuid::INVALID_HYPHEN_PLACEMENT_ERROR => 493,
        Assert\Uuid::INVALID_VARIANT_ERROR => 494,
        Assert\Uuid::INVALID_VERSION_ERROR => 495,
    ];

    /** @var array<string, int> */
    protected array $paramMap;

    /**
     * @param array<string, int> $paramMap
     */
    public function __construct(array $paramMap)
    {
        $this->paramMap = $paramMap;
    }

    /**
     * @param ErrorCollection|null $errors
     * @return int
     */
    public function getStatusCode(?ErrorCollection $errors = null): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    /**
     * @param ConstraintViolationInterface $violation
     * @return Error|null
     */
    public function transform(ConstraintViolationInterface $violation): ?Error
    {
        $path = preg_replace('/\[.*?\]/', '', $violation->getPropertyPath());
        if (!isset($this->paramMap[$path])) {
            return null;
        }

        return (new Error(
            $this->violationToErrorCode($violation->getCode() ?? Assert\Type::INVALID_TYPE_ERROR),
            $violation->getMessage()
        ))->setParam($this->paramMap[$path]);
    }

    /**
     * @param string $code
     * @return int
     */
    protected function violationToErrorCode(string $code): int
    {
        return self::VIOLATION_TO_ERROR_CODE[$code];
    }
}
