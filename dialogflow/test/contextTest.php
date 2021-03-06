<?php
/**
 * Copyright 2018 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


namespace Google\Cloud\Samples\Dialogflow;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * Unit Tests for context management commands.
 */
class contextTest extends \PHPUnit_Framework_TestCase
{
    private static $projectId;
    private static $contextId;
    private static $sessionId = 'fake_session_for_testing';

    public static function setUpBeforeClass()
    {
        if (!self::$projectId = getenv('GOOGLE_PROJECT_ID')) {
            return $this->markTestSkipped('Set the GOOGLE_PROJECT_ID ' .
                'environment variable');
        }

        if (!$creds = getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
            $this->markTestSkipped('Set the GOOGLE_APPLICATION_CREDENTIALS ' .
                'environment variable');
        }
        self::$contextId = 'fake_context_for_testing_' . time();
    }

    public function testCreateContext()
    {
        $this->runCommand('context-create', [
            'context-id' => self::$contextId
        ]);
        $output = $this->runCommand('context-list');

        $this->assertContains(self::$contextId, $output);
    }

    /** @depends testCreateContext */
    public function testDeleteContext()
    {
        $this->runCommand('context-delete', [
            'context-id' => self::$contextId
        ]);
        $output = $this->runCommand('context-list');

        $this->assertNotContains(self::$contextId, $output);
    }

    private function runCommand($commandName, $args=[])
    {
        $application = require __DIR__ . '/../dialogflow.php';
        $command = $application->get($commandName);
        $commandTester = new CommandTester($command);
        ob_start();
        $commandTester->execute(
            $args + [
                'project-id' => self::$projectId,
                '--session-id' => self::$sessionId
            ],
            ['interactive' => false]
        );
        return ob_get_clean();
    }
}
