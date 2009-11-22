# OpenGoo model for BambooInvoice

The purpose of this branch is to experiment bridging of BambooInvoice on OpenGoo,
by making the first use the database tables of the latter.

This is experimental is coded for free. It is not extensively tested due to
time constraints. You **ought** to try the demo before deciding to install
this.

## B2C Branch

The concept of the B2C branch is that both OG companies and OG contacts are
clients in BI.

## Demo

You **must** try the demo first, and make sure that everything work as expected.

<table>
    <thead>
        <tr>
            <th>Application</th>
            <th>Url</th>
            <th>Username</th>
            <th>Password</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>OpenGoo, vanilla</td>
            <td>http://goo.ocpsys.com</td>
            <td>jpic</td>
            <td>demo</td>
        </tr>
        <tr>
            <td>BambooInvoice, patched</td>
            <td>http://boo.ocpsys.com</td>
            <td>demo@example.com</td>
            <td>demo</td>
        </tr>
    </tbody>
</table>

# Setup

You **must** do full database and files backup before installing experimental
patches.

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
