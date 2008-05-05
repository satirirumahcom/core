<?php
/**
 * 
 * List of all reserved words for all supported databases.
 * 
 * @category Solar
 * 
 * @package Solar_Sql
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */
class Solar_Sql_Reserved
{
    /**
     * 
     * The array of words reserved across all supported databases.
     * 
     * @var array
     * 
     */
    public $words = array(
        '_ROWID_', 'ABSOLUTE', 'ACCESS', 'ACTION', 'ADD', 'ADMIN',
        'AFTER', 'AGGREGATE', 'ALIAS', 'ALL', 'ALLOCATE', 'ALTER',
        'ANALYSE', 'ANALYZE', 'AND', 'ANY', 'ARE', 'ARRAY', 'AS', 'ASC',
        'ASENSITIVE', 'ASSERTION', 'AT', 'AUDIT', 'AUTHORIZATION',
        'AUTO_INCREMENT', 'AVG', 'BACKUP', 'BDB', 'BEFORE', 'BEGIN',
        'BERKELEYDB', 'BETWEEN', 'BIGINT', 'BINARY', 'BIT',
        'BIT_LENGTH', 'BLOB', 'BOOLEAN', 'BOTH', 'BREADTH', 'BREAK',
        'BROWSE', 'BULK', 'BY', 'CALL', 'CASCADE', 'CASCADED', 'CASE',
        'CAST', 'CATALOG', 'CHANGE', 'CHAR', 'CHAR_LENGTH', 'CHARACTER',
        'CHARACTER_LENGTH', 'CHECK', 'CHECKPOINT', 'CLASS', 'CLOB',
        'CLOSE', 'CLUSTER', 'CLUSTERED', 'COALESCE', 'COLLATE',
        'COLLATION', 'COLUMN', 'COLUMNS', 'COMMENT', 'COMMIT',
        'COMPLETION', 'COMPRESS', 'COMPUTE', 'CONDITION', 'CONNECT',
        'CONNECTION', 'CONSTRAINT', 'CONSTRAINTS', 'CONSTRUCTOR',
        'CONTAINS', 'CONTAINSTABLE', 'CONTINUE', 'CONVERT',
        'CORRESPONDING', 'COUNT', 'CREATE', 'CROSS', 'CUBE', 'CURRENT',
        'CURRENT_DATE', 'CURRENT_PATH', 'CURRENT_ROLE', 'CURRENT_TIME',
        'CURRENT_TIMESTAMP', 'CURRENT_USER', 'CURSOR', 'CYCLE', 'DATA',
        'DATABASE', 'DATABASES', 'DATE', 'DAY', 'DAY_HOUR',
        'DAY_MICROSECOND', 'DAY_MINUTE', 'DAY_SECOND', 'DBCC',
        'DEALLOCATE', 'DEC', 'DECIMAL', 'DECLARE', 'DEFAULT',
        'DEFERRABLE', 'DEFERRED', 'DELAYED', 'DELETE', 'DENY', 'DEPTH',
        'DEREF', 'DESC', 'DESCRIBE', 'DESCRIPTOR', 'DESTROY',
        'DESTRUCTOR', 'DETERMINISTIC', 'DIAGNOSTICS', 'DICTIONARY',
        'DISCONNECT', 'DISK', 'DISTINCT', 'DISTINCTROW', 'DISTRIBUTED',
        'DIV', 'DO', 'DOMAIN', 'DOUBLE', 'DROP', 'DUMMY', 'DUMP',
        'DYNAMIC', 'EACH', 'ELSE', 'ELSEIF', 'ENCLOSED', 'END',
        'END-EXEC', 'EQUALS', 'ERRLVL', 'ESCAPE', 'ESCAPED', 'EVERY',
        'EXCEPT', 'EXCEPTION', 'EXCLUSIVE', 'EXEC', 'EXECUTE', 'EXISTS',
        'EXIT', 'EXPLAIN', 'EXTERNAL', 'EXTRACT', 'FALSE', 'FETCH',
        'FIELDS', 'FILE', 'FILLFACTOR', 'FIRST', 'FLOAT', 'FOR',
        'FORCE', 'FOREIGN', 'FOUND', 'FRAC_SECOND', 'FREE', 'FREETEXT',
        'FREETEXTTABLE', 'FREEZE', 'FROM', 'FULL', 'FULLTEXT',
        'FUNCTION', 'GENERAL', 'GET', 'GLOB', 'GLOBAL', 'GO', 'GOTO',
        'GRANT', 'GROUP', 'GROUPING', 'HAVING', 'HIGH_PRIORITY',
        'HOLDLOCK', 'HOST', 'HOUR', 'HOUR_MICROSECOND', 'HOUR_MINUTE',
        'HOUR_SECOND', 'IDENTIFIED', 'IDENTITY', 'IDENTITY_INSERT',
        'IDENTITYCOL', 'IF', 'IGNORE', 'ILIKE', 'IMMEDIATE', 'IN',
        'INCREMENT', 'INDEX', 'INDICATOR', 'INFILE', 'INITIAL',
        'INITIALIZE', 'INITIALLY', 'INNER', 'INNODB', 'INOUT', 'INPUT',
        'INSENSITIVE', 'INSERT', 'INT', 'INTEGER', 'INTERSECT',
        'INTERVAL', 'INTO', 'IO_THREAD', 'IS', 'ISNULL', 'ISOLATION',
        'ITERATE', 'JOIN', 'KEY', 'KEYS', 'KILL', 'LANGUAGE', 'LARGE',
        'LAST', 'LATERAL', 'LEADING', 'LEAVE', 'LEFT', 'LESS', 'LEVEL',
        'LIKE', 'LIMIT', 'LINENO', 'LINES', 'LOAD', 'LOCAL',
        'LOCALTIME', 'LOCALTIMESTAMP', 'LOCATOR', 'LOCK', 'LONG',
        'LONGBLOB', 'LONGTEXT', 'LOOP', 'LOW_PRIORITY', 'LOWER', 'MAIN',
        'MAP', 'MASTER_SERVER_ID', 'MATCH', 'MAX', 'MAXEXTENTS',
        'MEDIUMBLOB', 'MEDIUMINT', 'MEDIUMTEXT', 'MIDDLEINT', 'MIN',
        'MINUS', 'MINUTE', 'MINUTE_MICROSECOND', 'MINUTE_SECOND',
        'MLSLABEL', 'MOD', 'MODE', 'MODIFIES', 'MODIFY', 'MODULE',
        'MONTH', 'NAMES', 'NATIONAL', 'NATURAL', 'NCHAR', 'NCLOB',
        'NEW', 'NEXT', 'NO', 'NO_WRITE_TO_BINLOG', 'NOAUDIT', 'NOCHECK',
        'NOCOMPRESS', 'NONCLUSTERED', 'NONE', 'NOT', 'NOTNULL',
        'NOWAIT', 'NULL', 'NULLIF', 'NUMBER', 'NUMERIC', 'OBJECT',
        'OCTET_LENGTH', 'OF', 'OFF', 'OFFLINE', 'OFFSET', 'OFFSETS',
        'OID', 'OLD', 'ON', 'ONLINE', 'ONLY', 'OPEN', 'OPENDATASOURCE',
        'OPENQUERY', 'OPENROWSET', 'OPENXML', 'OPERATION', 'OPTIMIZE',
        'OPTION', 'OPTIONALLY', 'OR', 'ORDER', 'ORDINALITY', 'OUT',
        'OUTER', 'OUTFILE', 'OUTPUT', 'OVER', 'OVERLAPS', 'PAD',
        'PARAMETER', 'PARAMETERS', 'PARTIAL', 'PATH', 'PCTFREE',
        'PERCENT', 'PLACING', 'PLAN', 'POSITION', 'POSTFIX',
        'PRECISION', 'PREFIX', 'PREORDER', 'PREPARE', 'PRESERVE',
        'PRIMARY', 'PRINT', 'PRIOR', 'PRIVILEGES', 'PROC', 'PROCEDURE',
        'PUBLIC', 'PURGE', 'RAISERROR', 'RAW', 'READ', 'READS',
        'READTEXT', 'REAL', 'RECONFIGIGURE', 'RECURSIVE', 'REF',
        'REFERENCES', 'REFERENCING', 'REGEXP', 'RELATIVE', 'RENAME',
        'REPEAT', 'REPLACE', 'REPLICATION', 'REQUIRE', 'RESOURCE',
        'RESTORE', 'RESTRICT', 'RESULT', 'RETURN', 'RETURNS', 'REVOKE',
        'RIGHT', 'RLIKE', 'ROLE', 'ROLLBACK', 'ROLLUP', 'ROUTINE',
        'ROW', 'ROWCOUNT', 'ROWGUIDCOL', 'ROWID', 'ROWNUM', 'ROWS',
        'RULE', 'SAVE', 'SAVEPOINT', 'SCHEMA', 'SCOPE', 'SCROLL',
        'SEARCH', 'SECOND', 'SECOND_MICROSECOND', 'SECTION', 'SELECT',
        'SENSITIVE', 'SEPARATOR', 'SEQUENCE', 'SESSION', 'SESSION_USER',
        'SET', 'SETS', 'SETUSER', 'SHARE', 'SHOW', 'SHUTDOWN',
        'SIMILAR', 'SIZE', 'SMALLINT', 'SOME', 'SONAME', 'SPACE',
        'SPATIAL', 'SPECIFIC', 'SPECIFICTYPE', 'SQL', 'SQL_BIG_RESULT',
        'SQL_CALC_FOUND_ROWS', 'SQL_SMALL_RESULT', 'SQL_TSI_DAY',
        'SQL_TSI_FRAC_SECOND', 'SQL_TSI_HOUR', 'SQL_TSI_MINUTE',
        'SQL_TSI_MONTH', 'SQL_TSI_QUARTER', 'SQL_TSI_SECOND',
        'SQL_TSI_WEEK', 'SQL_TSI_YEAR', 'SQLCODE', 'SQLERROR',
        'SQLEXCEPTION', 'SQLITE_MASTER', 'SQLITE_TEMP_MASTER',
        'SQLSTATE', 'SQLWARNING', 'SSL', 'START', 'STARTING', 'STATE',
        'STATEMENT', 'STATIC', 'STATISTICS', 'STRAIGHT_JOIN', 'STRIPED',
        'STRUCTURE', 'SUBSTRING', 'SUCCESSFUL', 'SUM', 'SYNONYM',
        'SYSDATE', 'SYSTEM_USER', 'TABLE', 'TABLES', 'TEMPORARY',
        'TERMINATE', 'TERMINATED', 'TEXTSIZE', 'THAN', 'THEN', 'TIME',
        'TIMESTAMP', 'TIMESTAMPADD', 'TIMESTAMPDIFF', 'TIMEZONE_HOUR',
        'TIMEZONE_MINUTE', 'TINYBLOB', 'TINYINT', 'TINYTEXT', 'TO',
        'TOP', 'TRAILING', 'TRAN', 'TRANSACTION', 'TRANSLATE',
        'TRANSLATION', 'TREAT', 'TRIGGER', 'TRIM', 'TRUE', 'TRUNCATE',
        'TSEQUAL', 'UID', 'UNDER', 'UNDO', 'UNION', 'UNIQUE', 'UNKNOWN',
        'UNLOCK', 'UNNEST', 'UNSIGNED', 'UPDATE', 'UPDATETEXT', 'UPPER',
        'USAGE', 'USE', 'USER', 'USER_RESOURCES', 'USING', 'UTC_DATE',
        'UTC_TIME', 'UTC_TIMESTAMP', 'VALIDATE', 'VALUE', 'VALUES',
        'VARBINARY', 'VARCHAR', 'VARCHAR2', 'VARCHARACTER', 'VARIABLE',
        'VARYING', 'VERBOSE', 'VIEW', 'WAITFOR', 'WHEN', 'WHENEVER',
        'WHERE', 'WHILE', 'WITH', 'WITHOUT', 'WORK', 'WRITE',
        'WRITETEXT', 'XOR', 'YEAR', 'YEAR_MONTH', 'ZEROFILL', 'ZONE',
    );
}
