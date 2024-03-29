Feature: Manage maintenance mode of WordPress install.

  Background:
    Given a WP install

  Scenario: Manage maintenance mode.

    When I run `wp maintenance-mode status`
    Then STDOUT should be:
      """
      Maintenance mode is not active.
      """

    When I run `wp maintenance-mode activate`
    Then STDOUT should be:
      """
      Enabling Maintenance mode...
      Success: Activated Maintenance mode.
      """

    When I run `wp maintenance-mode is-active`
    Then the return code should be 0

    When I run `wp maintenance-mode status`
    Then STDOUT should be:
      """
      Maintenance mode is active.
      """

    When I try `wp maintenance-mode activate`
    Then STDERR should be:
      """
      Error: Maintenance mode already activated.
      """

    When I run `wp maintenance-mode activate --force`
    Then STDOUT should be:
      """
      Enabling Maintenance mode...
      Success: Activated Maintenance mode.
      """

    When I run `wp maintenance-mode deactivate`
    Then STDOUT should be:
      """
      Disabling Maintenance mode...
      Success: Deactivated Maintenance mode.
      """

    When I try `wp maintenance-mode is-active`
    Then the return code should be 1

    When I try `wp maintenance-mode deactivate`
    Then STDERR should be:
      """
      Error: Maintenance mode already deactivated.
      """

    When I run `wp maintenance-mode activate`
    Then STDOUT should be:
      """
      Enabling Maintenance mode...
      Success: Activated Maintenance mode.
      """

  Scenario: Check maintenance mode status when expression is used.

    When I run `wp eval "file_put_contents('.maintenance', '<?php \$upgrading=(time()-601);'); "`
    And I try `wp maintenance-mode is-active`
    Then the return code should be 1
    And STDERR should contain:
      """
      Warning: Unable to read the maintenance file timestamp, non-numeric value detected.
      """

  Scenario: Check maintenance mode status when numeric timestamp is used.

    When I run `wp eval "file_put_contents('.maintenance', '<?php \$upgrading=' . ( time() + 100 ) . ';'); "`
    And I run `wp maintenance-mode is-active`
    Then the return code should be 0

    When I run `wp eval "file_put_contents('.maintenance', '<?php \$upgrading =' . ( time() + 100 )  . ';')  ; "`
    And I run `wp maintenance-mode is-active`
    Then the return code should be 0

    When I run `wp eval "file_put_contents('.maintenance', '<?php \$upgrading= ' . ( time() + 100 )  . ';'); "`
    And I run `wp maintenance-mode is-active`
    Then the return code should be 0
