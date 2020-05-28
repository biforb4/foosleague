Feature: Create players

  Scenario:
    When I create player with "@Player" Slack Handle
    Then A player is created and its name is "@Player"
