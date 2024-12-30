<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePresensiTables extends Migration
{
    public function up()
    {
        // Table: department
        $this->forge->addField([
            'department_id' => [
                'type' => 'CHAR',
                'constraint' => 3,
                'null' => false,
            ],
            'department_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('department_id', true);
        $this->forge->createTable('department');

        // Table: shift
        $this->forge->addField([
            'shift_id' => [
                'type' => 'INT',
                'constraint' => 1,
                'auto_increment' => true,
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('shift_id', true);
        $this->forge->createTable('shift');

        // Table: employee
        $this->forge->addField([
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 4,
                'unsigned' => true,
                'zerofill' => true,
                'auto_increment' => true,
            ],
            'employee_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['Laki-Laki', 'Perempuan'],
                'null' => false,
            ],
            'marital_status' => [
                'type' => 'ENUM',
                'constraint' => ['Belum Kawin', 'Kawin', 'Janda/Duda'],
                'null' => false,
            ],
            'num_children' => [
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => true,
                'default' => 0,
                'null' => true,
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => true,
            ],
            'education' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'employee_address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'telephone' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'birth_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'hire_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'contraceptive_use' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'rptra_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'rptra_address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'shift_id' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => false,
            ],
            'department_id' => [
                'type' => 'CHAR',
                'constraint' => 3,
                'null' => false,
            ],
        ]);
        $this->forge->addKey('employee_id', true);
        $this->forge->addForeignKey('shift_id', 'shift', 'shift_id', 'CASCADE', 'SET NOT NULL');
        $this->forge->addForeignKey('department_id', 'department', 'department_id', 'CASCADE', 'SET NOT NULL');
        $this->forge->createTable('employee');

        // Table: user_role
        $this->forge->addField([
            'user_role_id' => [
                'type' => 'INT',
                'constraint' => 1,
                'auto_increment' => true,
            ],
            'user_role_name' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
        ]);
        $this->forge->addKey('user_role_id', true);
        $this->forge->createTable('user_role');

        // Table: user_accounts
        $this->forge->addField([
            'username' => [
                'type' => 'CHAR',
                'constraint' => 7,
                'null' => false,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 4,
                'unsigned' => true,
                'zerofill' => true,
                'null'     => false,
            ],
            'user_role_id' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => false,
            ],
        ]);
        $this->forge->addKey('username', true);
        $this->forge->addForeignKey('employee_id', 'employee', 'employee_id', 'CASCADE', 'SET NOT NULL');
        $this->forge->addForeignKey('user_role_id', 'user_role', 'user_role_id', 'CASCADE', 'SET NOT NULL');
        $this->forge->createTable('user_accounts');

        // Table: attendance
        $this->forge->addField([
            'attendance_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'username' => [
                'type' => 'CHAR',
                'constraint' => 7,
                'null' => false,
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 4,
                'unsigned' => true,
                'zerofill' => true,
                'null' => false,
            ],
            'department_id' => [
                'type' => 'CHAR',
                'constraint' => 3,
                'null' => false,
            ],
            'shift_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'attendance_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'in_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'in_status' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => true,
            ],
            'out_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'presence_status' => [
                'type' => 'INT',
                'constraint' => 1,
                'default' => 0,
            ],
            'check_in_latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => true,
            ],
            'check_in_longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => true,
            ],
            'check_out_latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => true,
            ],
            'check_out_longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => true,
            ],    
        ]);
        $this->forge->addKey('attendance_id', true);
        $this->forge->addForeignKey('username', 'user_accounts', 'username', 'CASCADE', 'SET NOT NULL');
        $this->forge->addForeignKey('employee_id', 'employee', 'employee_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('department_id', 'department', 'department_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('shift_id', 'shift', 'shift_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attendance');
    }

    public function down()
    {
        $this->forge->dropTable('attendance');
        $this->forge->dropTable('user_accounts');
        $this->forge->dropTable('user_role');
        $this->forge->dropTable('employee');
        $this->forge->dropTable('shift');
        $this->forge->dropTable('department');
    }
}
