<?php
/* Prefix of OpenGoo table names. For example, is the name of the contacts
 * table is 'og_contacts' then the prefix is 'og_'. If the name of the table is
 * just 'contacts' then the prefix is '';
 *
 * Using no prefix, or the same prefix on both OpenGoo and BambooInvoice
 * database might be a problem.
 */
$db['default']['goo_table_prefix'] = 'og_';

/* OpenGoo contacts and companies are on different tables, with different
 * primary keys (row ids). Contact #2 is not the same as Company #2.
 *
 * So for BambooInvoice, with goo_b2c_contact_diff = 1000000:
 * - OG Company #2 is BI Client #2,
 * - OG Contact #2 is BI Client #1000002,
 */
$db['default']['goo_b2c_contact_diff'] = 1000000;

/* Updates on OpenGoo database can be disabled by setting goo_db_update to
 * false.
 */
$db['default']['goo_db_update'] = true;
?>
