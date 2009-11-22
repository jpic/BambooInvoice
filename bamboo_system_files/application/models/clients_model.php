<?php
/**
 * File containing class clients_model with opengoo modifications.
 *
 * First, setup mysql database with the following queries:
 * <code>
 * -- replace og_ by your OpenGoo table prefix if any.
 * alter table og_object_properties modify name varchar(255) null;
 * alter table og_object_properties add unique key (rel_object_id, rel_object_manager, name);
 * </code>
 * Then, replace bamboo_system_files/application/models/clients_model.php with
 * this file.
 *
 * @copyright Copyright (C) 2009 James Pic. All rights reserved.
 * @author James Pic <jamespic@gmail.com> 
 * @license Apache
 */
/**
 * This client model class uses both the contact and company database as client
 * which makes it convenient for b2c.
 *
 * Updating the name of a contact does not work because we can't know what part
 * of the "name" input would go in first_name or last_name of OpenGoo contacts
 * table.
 * 
 * Adding a client in Bamboo is not supported neither.
 *
 * @copyright Copyright (C) 2009 James Pic. All rights reserved.
 * @author James Pic <jamespic@gmail.com> 
 * @license Apache
 */
class clients_model extends Model {
    function countAllClients()
	{
        $sql = 'select sum( number) as total_clients 
            from ( 
                select count( id) as number from '.$this->db->goo_table_prefix.'contacts 
                union ( 
                    select count( id) as number from '.$this->db->goo_table_prefix.'companies
                )
            ) tmp';
        return $this->db->query( $sql )->row( 'total_clients' );
	}

	// --------------------------------------------------------------------

	function countClientInvoices($client_id)
	{
		$this->db->where('client_id', $client_id);

		return $this->db->count_all_results('invoices');
	}

	// --------------------------------------------------------------------

	function getAllClients()
	{
        return $this->db->query( '
            select
                ogc.id,
                ogc.name, 
                ogc.address as address1,
                ogc.address2 as address2,
                ogc.city,
                ogc.state as province,
                ogc.zipcode as postal_code, 
                ogc.country as country, 
                ogc.homepage as website,
                ogc.notes as client_notes, 
                ogts.value as tax_status, 
                ogtc.value as tax_code 
            from '. $this->db->goo_table_prefix .'companies as ogc
            left join ( 
                    select *
                    from '. $this->db->goo_table_prefix .'object_properties
                    where name="tax_status" and 
                        rel_object_manager = "Companies"
                ) ogts on ogts.rel_object_id = ogc.id
            left join ( 
                    select * 
                    from '. $this->db->goo_table_prefix .'object_properties 
                    where name="tax_code" and 
                        rel_object_manager = "Companies"
                ) ogtc on ogtc.rel_object_id = ogc.id
            union(
                select 
                    ogct.id + 1000000 as id,
                    concat(
                        ogct.firstname,
                        " ", 
                        ogct.lastname
                    ) as name,
                    ogct.h_address as address1,
                    "" as address2,
                    ogct.h_city as city,
                    ogct.h_state as province,
                    ogct.h_zipcode as postal_code,
                    ogct.h_country as country,
                    ogct.h_web_page as website, 
                    ogct.notes as client_notes,
                    ogts.value as tax_status,
                    ogtc.value as tax_code
                from '. $this->db->goo_table_prefix .'contacts ogct
                left join ( 
                        select * 
                        from '. $this->db->goo_table_prefix .'object_properties 
                        where name="tax_status" and
                            rel_object_manager = "Contacts"
                    ) ogts on ogts.rel_object_id = ogct.id
                left join ( 
                        select * 
                        from '. $this->db->goo_table_prefix .'object_properties 
                        where name="tax_code" and 
                            rel_object_manager = "Contacts"
                    ) ogtc on ogtc.rel_object_id = ogct.id
                )
        ' );

        $this->db->orderby('name', 'asc');
		return $this->db->get('clients');
	}

	// --------------------------------------------------------------------

	function get_client_info($id, $fields = '*')
	{
        // bare security
        if ( function_exists( 'filter_var' ) ) {
            $id = filter_var( $id, FILTER_SANITIZE_NUMBER_INT );
        } else {
            $id = intval( $id );
        }

        if ( $id > $this->db->goo_b2c_contact_diff ) {
            $id -= $this->db->goo_b2c_contact_diff;
            $select = '
                select
                    ogc.id + 1000000 as id,
                    concat(
                        ogc.firstname,
                        " ", 
                        ogc.lastname
                    ) as name,
                    ogc.h_address as address1,
                    "" as address2,
                    ogc.h_city as city,
                    ogc.h_state as province,
                    ogc.h_zipcode as postal_code,
                    ogc.h_country as country,
                    ogc.h_web_page as website, 
                    ogc.notes as client_notes,
                    ogts.value as tax_status,
                    ogtc.value as tax_code
                from '. $this->db->goo_table_prefix .'contacts ogc
                left join ( 
                        select * 
                        from '. $this->db->goo_table_prefix .'object_properties 
                        where name="tax_status" and 
                            rel_object_manager = "Contacts"
                    ) ogts on ogts.rel_object_id = ogc.id
                left join ( 
                        select * 
                        from '. $this->db->goo_table_prefix .'object_properties 
                        where name="tax_code" and 
                            rel_object_manager = "Contacts"
                    ) ogtc on ogtc.rel_object_id = ogc.id
                ';
        } else {
            $select = '
            select
                ogc.id,
                ogc.name, 
                ogc.address as address1,
                ogc.address2 as address2,
                ogc.city,
                ogc.state as province,
                ogc.zipcode as postal_code, 
                ogc.country as country, 
                ogc.homepage as website,
                ogc.notes as client_notes, 
                ogts.value as tax_status, 
                ogtc.value as tax_code 
            from '. $this->db->goo_table_prefix .'companies as ogc
            left join ( 
                    select * 
                    from '. $this->db->goo_table_prefix .'object_properties 
                    where name="tax_status" and 
                        rel_object_manager = "Companies"
                ) ogts on ogts.rel_object_id = ogc.id
            left join ( 
                    select * 
                    from '. $this->db->goo_table_prefix .'object_properties 
                    where name="tax_code" and 
                        rel_object_manager = "Companies"
                ) ogtc on ogtc.rel_object_id = ogc.id
            ';
        }

        return $this->db->query( $select . ' where ogc.id = ' . intval( $id ) )->row(  );

		$this->db->select($fields);
		$this->db->where('id', $id);

		return $this->db->get('clients')->row();
	}

	// --------------------------------------------------------------------

	function getClientContacts($id)
	{
		$this->db->where('client_id', $id);

		return $this->db->get('clientcontacts');
	}

	// --------------------------------------------------------------------

	function addClient($clientInfo)
	{
		$this->db->insert('clients', $clientInfo);

		return TRUE;
	}

	// --------------------------------------------------------------------

	function updateClient($client_id, $clientInfo)
	{
        $id = $client_id;

        if ( $id > $this->db->goo_b2c_contact_diff ) {
            $id -= $this->db->goo_b2c_contact_diff;
            $manager = 'Contacts';

            $update = array(
                'h_address'  => $clientInfo['address1'],
                'h_city'     => $clientInfo['city'],
                'h_state'    => $clientInfo['province'],
                'h_zipcode'  => $clientInfo['postal_code'],
                'h_country'  => $clientInfo['country'],
                'h_web_page' => $clientInfo['website'],
            );
            $table = 'contacts';
        } else {
            $manager = 'Companies';

            $update = array(
                'address'  => $clientInfo['address1'],
                'address2' => $clientInfo['address2'],
                'city'     => $clientInfo['city'],
                'state'    => $clientInfo['province'],
                'zipcode'  => $clientInfo['postal_code'],
                'country'  => $clientInfo['country'],
                'homepage' => $clientInfo['website'],
            );
            $table = 'companies';
        }

        $sql = ' update ' . $this->db->goo_table_prefix . $table . ' set ';
        $updateSql = array();
        foreach( $update as $col => $val ) {
            $updateSql[] = sprintf( '%s="%s"', $col, $val );
        }
        $sql .= join( ', ', $updateSql );
        $sql .= ' where id = ' . $id;
        //var_dump( $sql );
        $this->db->query( $sql );


        $baseSql = 'insert into '. $this->db->goo_table_prefix . 'object_properties 
            (rel_object_id, rel_object_manager, name, value)
            values( ' . $id . ', "' . $manager . '", "%s", "%s")
            on duplicate key update value=values(value)';
        $this->db->query( sprintf( $baseSql, 'tax_code', $clientInfo['tax_code'] ) );
        $this->db->query( sprintf( $baseSql, 'tax_status', $clientInfo['tax_status'] ) );

		return TRUE;
	}

	// --------------------------------------------------------------------

	function deleteClient($client_id)
	{
        trigger_error( 'Deleting clients is not supported' );

		// Don't allow admins to be deleted this way
		if ($client_id === 0)
		{
			return FALSE;
		}
		else
		{
			// get all invoices related to this client
			$this->db->select('id');
			$this->db->where('client_id', $client_id);
			$result = $this->db->get('invoices');

			$invoice_id_array = array(0);

			foreach ($result->result() as $invoice_id)
			{
				$invoice_id_array[] = $invoice_id->id;
			}

			// There are 5 tables of data to delete from in order to completely
			// clear out record of this client.

			$this->db->where_in('invoice_id', $invoice_id_array);
			$this->db->delete('invoice_histories');

			$this->db->where_in('invoice_id', $invoice_id_array);
			$this->db->delete('invoice_payments');

			$this->db->where('client_id', $client_id);
			$this->db->delete('clientcontacts'); 

			$this->db->where('id', $client_id);
			$this->db->delete('clients');

			$this->db->where('client_id', $client_id);
			$this->db->delete('invoices'); 

			return TRUE;
		}
	}

}
?>
