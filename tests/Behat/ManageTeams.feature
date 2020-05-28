Feature: Create team
  Rules:
  - Each team has to have exactly two players

  Scenario:
    When I create a "Team blue" team
    Then It's name is "Team blue"
    And Has two players
