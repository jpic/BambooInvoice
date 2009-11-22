# OpenGoo model for BambooInvoice

The purpose of this branch is to experiment bridging of BambooInvoice on OpenGoo,
by making the first use the database tables of the latter.

## B2C Branch

The concept of the B2C branch is that both OG companies and OG contacts are
clients in BI.

## Demo

| Application | Url | Username | Password |
| OpenGoo, vanilla | http://goo.ocpsys.com/ | jpic | demo |
| BambooInvoice, patched | http://boo.ocpsys.com/ | demo@example.com | demo |

# Setup

## Database upgrade

The bridge requires a database to update OpenGoo tables. The database update
looks backward compatible, but no extensive quality tests was executed on
OpenGoo after this update.

There is no need to do this if configuration setting goo\_db\_update is set to
false.

Skip this step if you want to disable OpenGoo database write from BambooInvoice.

Run the following SQL code on the database if you want updates on OG contacts
and companies be doable from BI.

If goo\_db\_update is set to true, please execute the following SQL after
updating the table names to your setup:

<pre>
-- replace og_ by your OpenGoo table prefix if any.
alter table og_object_properties modify name varchar(255) null;
alter table og_object_properties add unique key (rel_object_id, rel_object_manager, name);
</pre>

## Configuration

To configure the bridge:

* Add the include statement in application/config/database.php like [so](http://github.com/jpic/BambooInvoice/blob/b2c/bamboo_system_files/application/config/database.php#L50),
* Download the default configuration file by clicking the *raw* link on [this page](http://github.com/jpic/BambooInvoice/blob/b2c/bamboo_system_files/application/config/opengoo.php),
* Move the default configuration file to application/config/opengoo.php,
  the config folder should then look like [this](http://github.com/jpic/BambooInvoice/tree/b2c/bamboo_system_files/application/config),
* Read the default configuration and adapt it to your need.

## Model

* Download the clients model class from [here](http://github.com/jpic/BambooInvoice/blob/b2c/bamboo_system_files/application/models/clients_model.php),
* Replace your clients model class with the downloaded file, in application/models/clients_model.php.

# Quotes

Capitan:
> I really hate having to create a duplicate entry for clients in bamboo after i created them already in opengoo.

# Credits

* Derek Allard is the author of BambooInvoice - nice code!
* Capitan contributed many reviews of the bridge.
* Feng Office is the maintener of OpenGoo - the best online office!

Thanks!
