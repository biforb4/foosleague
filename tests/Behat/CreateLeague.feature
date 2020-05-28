Feature: Creating a league
#TODO: Add owner and sad path scenarios
  @slack
  Scenario: Creating a league
    When I create a league "Office League"
    Then "Office League" league is created

  Scenario: Adding a team
    Given A league exists
    And Team "Team blue" exists
    When I add "Team blue" to that league
    Then "Team blue" is added to the league

  Scenario: Removing a team
    Given A league exists
    And Team "Team blue" exists
    And that league league has "Team blue"
    When I remove "Team blue" from that league
    Then "Team blue" is removed from that league

