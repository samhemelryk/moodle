<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Test XMLDB install.xml files
 *
 * @package tool_xmldb
 * @copyright 2020 Totara Learning Solutions
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Test case to verify that all install.xml files are correct.
 *
 * sam.hemelryk@totaralearning.com
 *
 *   Reasons to push this as a requirements:
 *
 *    0. Good developers use the XMLDB tool. They care.
 *    1. Otherwise next developer, who does their job properly
 *       finds the file contains unintended changes when they save
 *       from the XMLDB editor, and either they have to revert the
 *       mess, or commit a messy patch.
 *    2. Reviewers and testers can trust that the INSTALL.xml file
 *       is correctly formatted without needing to edit and save it
 *       to verify.
 *    3. Who maintains old install.xml files? No one, they don't
 *       need maintaining. Until you change the XMLDB schema. This
 *       test will ensure install.xml files are updated when the
 *       schema gets updated and that third party devs are informed
 *       of the change when running tests in their own plugins.
 *
 * Don't be lazy. Use the editor. It's there for a reason.
 *
 * @package tool_xmldb
 * @copyright 2020 Totara Learning Solutions
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_xmldb_instal_xml_testcase extends advanced_testcase {

    /**
     * Test install.xml files.
     *
     * There is a comment at the end of this test scenario that will fix
     * your install.xml files. However we strongly recommend you start
     * using the editor - it'll get it right for you every time.
     */
    public function test_all_install_xml_files_formatted_correctly() {
        global $CFG;

        require_once($CFG->dirroot . '/lib/adminlib.php');
        require_once($CFG->dirroot . '/admin/tool/xmldb/actions/XMLDBAction.class.php');
        require_once($CFG->dirroot . '/admin/tool/xmldb/actions/get_db_directories/get_db_directories.class.php');

        global $XMLDB;
        $XMLDB = new stdClass;

        $directories = new get_db_directories();
        $directories->invoke();

        $this->assertNotEmpty($XMLDB->dbdirs);

        foreach ($XMLDB->dbdirs as $directory) {

            if (!$directory->path_exists) {
                continue;
            }

            $file = $directory->path . '/install.xml';
            $xmldbfile = new xmldb_file($file);
            // Set the XML DTD and schema.
            $xmldbfile->setDTD($CFG->dirroot . '/lib/xmldb/xmldb.dtd');
            $xmldbfile->setSchema($CFG->dirroot . '/lib/xmldb/xmldb.xsd');
            // Set dbdir as necessary.
            if (!$xmldbfile->fileExists()) {
                continue;
            }
            // Load the XML contents to structure.
            $this->assertTrue($xmldbfile->loadXMLStructure(), "XMLDB file '{$file}' cannot be loaded, check the structure returned by this call for the error.");
            $this->assertTrue($xmldbfile->isLoaded(), "XMLDB file '{$file}' did not load correctly");
            $structure = $xmldbfile->getStructure();
            $this->assertNotNull($structure, "XMLDB file '{$file}' structure could not be parsed");
            $this->assertInstanceOf('xmldb_structure', $structure);

            $xml = $structure->xmlOutput();
            $this->assertIsString($xml);
            $this->assertNotEmpty($xml);

            // If for any reason you want to bulk save XML files in the correct format just comment this.
            // =========================================================
            //      file_put_contents($file, $xml);
            // =========================================================
            // Of course the next test will pass ;)

            $this->assertTrue(file_get_contents($file) === $xml, "XMLDB file '{$file}' is different to the generated version, please edit the file in XMLDB editor and save it.");
        }
    }
}