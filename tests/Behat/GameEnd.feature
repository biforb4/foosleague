Feature: Game ends
  Rules:
  - First team to win 2 sets wins
  - First team to score 10 points wins a set

  Scenario Outline:
    When the first set score is <firstSet>
    And the second set score is <secondSet>
    And the third set score is <thirdSet>
    Then the game ends <result>

    Examples:
      | firstSet | secondSet | thirdSet | result               |
      | 10:9     | 10:1      | no score | 2:0 (10:9 10:1)      |
      | 10:9     | 1:10      | 10:1     | 2:1 (10:9 1:10 10:1) |
      | 9:10     | 10:1      | 1:10     | 1:2 (9:10 10:1 1:10) |
      | 9:10     | 1:10      | no score | 0:2 (9:10 1:10)      |


  Scenario Outline: Last finished game in the league allows playoffs to start
    Given A league exists
    And started with <numberOfSignedUpTeam> teams
    When all games ended
    Then I can determine <numberOfPlayoffTeams> playoffs bound teams
    And I can not determine less than 2
    And I can not determine more than 8
    Examples:
      | numberOfSignedUpTeam | numberOfPlayoffTeams |
      | 3                    | 2                    |
      | 4                    | 4                    |
      | 7                    | 4                    |
      | 8                    | 8                    |


