<?php
namespace NGS;

abstract class Requirements
{
    public static function init()
    {
        $ngsStatics = array(
            'BigDecimal.php',
            'BigInt.php',
            'ByteStream.php',
            'LocalDate.php',
            'Money.php',
            'Name.php',
            'Timestamp.php',
            'Utils.php',
            'UUID.php',

            'Client/Exception/InvalidRequestException.php',
            'Client/Exception/NotFoundException.php',
            'Client/Exception/RequestException.php',
            'Client/Exception/SecurityException.php',

            'Client/ApplicationProxy.php',
            'Client/CrudProxy.php',
            'Client/DomainProxy.php',
            'Client/HttpRequest.php',
            'Client/ReportingProxy.php',
            'Client/RestHttp.php',
            'Client/StandardProxy.php',

            'Converter/ConverterInterface.php',
            'Converter/PrimitiveConverter.php',
            'Converter/BigDecimalConverter.php',
            'Converter/BigIntConverter.php',
            'Converter/ByteStreamConverter.php',
            'Converter/LocalDateConverter.php',
            'Converter/MoneyConverter.php',
            'Converter/ObjectConverter.php',
            'Converter/TimestampConverter.php',
            'Converter/UUIDConverter.php',
            'Converter/XmlConverter.php',

            'Patterns/IDomainObject.php',
            'Patterns/IIdentifiable.php',
            'Patterns/Identifiable.php',
            'Patterns/AggregateRoot.php',
            'Patterns/DomainEvent.php',
            'Patterns/Queryable.php',
            'Patterns/Snapshot.php',
            'Patterns/Snowflake.php',
            'Patterns/Specification.php'
        );

        foreach($ngsStatics as $req) {
            require_once Dirs::$modules.'NGS/'.$req;
        }
    }
}
