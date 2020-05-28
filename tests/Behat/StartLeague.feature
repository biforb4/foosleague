Feature: The league is started

  Background:
    Given A league exists
    And the following teams are in the league:
      | name        |
      | Team blue   |
      | Team red    |
      | Team black  |
      | Team yellow |

  Scenario:
    When The league is started
    Then the following schedule is generated
      | Home team  | Away team   |
      | Team blue  | Team red    |
      | Team blue  | Team black  |
      | Team blue  | Team yellow |
      | Team red   | Team black  |
      | Team red   | Team yellow |
      | Team black | Team yellow |
