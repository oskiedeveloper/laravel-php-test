<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Rule;
use App\Services\RuleEvaluator;

class RuleEngineTest extends TestCase
{
    use RefreshDatabase;

    protected RuleEvaluator $evaluator;
    protected User $user;
    protected Rule $rule;
    
    /**
     *  Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize a test user
        $this->user = User::factory()->create();

        // Initialize a test rules
        $this->rule = Rule::create([
            'action' => 'action_test',
            'rules' => [
                ['field' => 'role', 'operator' => '==', 'value' => 'user'],
                ['field' => 'email_verified_at', 'operator' => '!=', 'value' => null]
            ]
        ]);

        // Initialize the RuleEvaluator service
        $this->evaluator = new RuleEvaluator();
    }

    /**
     *  Tear down the test environment.
     */
    protected function tearDown(): void
    {
        // Clean up the test user and rules
        Rule::truncate();
        User::truncate();
        parent::tearDown();
    }

    /**
     *  Test Case:
     *  Check if a user can perform an action "submit_form" based on defined rules.
     *  It should return true if the user meets the rules, false otherwise.
     */
    public function test_user_can_submit_form_with_valid_rules()
    {   
        // Create a staff user
        $staffUser = User::factory()->create([
            'role' => 'staff',
            'email_verified_at' => now()
        ]);

        // Create a rules for the action 'submit_form'
        $ruleSet = Rule::create([
            'action' => 'submit_form',
            'rules' => [
                ['field' => 'role', 'operator' => '==', 'value' => 'staff'],
                ['field' => 'email_verified_at', 'operator' => '!=', 'value' => null]
            ]
        ]);

        // Action: Evaluate the rules for the action 'submit_form'
        $result = $this->evaluator->evaluate($staffUser, $ruleSet->rules);
        
        // Assert: Should return true as the user meets the rules
        $this->assertTrue($result); 
    }
    
    /**
     *  Test Case:
     *  Check if a user cannot perform an action "submit_form" when rules are not met.
     *  It should return false if the user does not meet the rules.
     */
    public function test_user_cannot_submit_form_with_invalid_rules()
    {
        // Create a regular user
        $regularUser = User::factory()->create([
            'role' => 'user',   
            'email_verified_at' => null // Not verified
        ]);

        // Create a rules with conditions that the user does not meet
        $ruleSet = Rule::create([
            'action' => 'submit_form',
            'rules' => [
                ['field' => 'role', 'operator' => '==', 'value' => 'admin'],
                ['field' => 'email_verified_at', 'operator' => '!=', 'value' => null]
            ]
        ]);

        // Action: Evaluate the rules for the action 'submit_form
        $result = $this->evaluator->evaluate($regularUser, $ruleSet->rules);
        
        // Assert: Should return false as the user does not meet the rules
        $this->assertFalse($result);
    }

    /** 
     *  Test Case:
     *  Check if a user can perform an action with multiple rules where one rule fails.
     *  It should return false if any rule is not met.
     */
    public function test_user_cannot_perform_action_with_one_rule_failing()
    {
        // Create a user with a role that does not meet the rules
        $user = User::factory()->create([
            'role' => 'guest',
            'email_verified_at' => now()
        ]);

        // Create a rules with one failing condition
        $ruleSet = Rule::create([
            'action' => 'submit_form',
            'rules' => [
                ['field' => 'role', 'operator' => '==', 'value' => 'admin'], // Failing rule
                ['field' => 'email_verified_at', 'operator' => '!=', 'value' => null]
            ]
        ]);

        // Action: Evaluate the rules for the action 'submit_form'
        $result = $this->evaluator->evaluate($user, $ruleSet->rules);
        
        // Assert: Should return false as the user does not meet the rules
        $this->assertFalse($result);
    }

    /**
     *  Test Case:
     *  Check if an unsupported operator throws an exception.
     *  It should throw an exception when an unsupported operator is used.
     */
    public function test_unsupported_operator_throws_exception()
    {
        // Create a user
        $user = User::factory()->create();

        // Define a rule with an unsupported operator
        $ruleSet = [
            ['field' => 'role', 'operator' => null, 'value' => 'user']
        ];

        // Assert: Expect an exception to be thrown
        $this->expectException(\InvalidArgumentException::class);
        
        // Action: Evaluate the rules
        $this->evaluator->evaluate($user, $ruleSet);
    }
    
}
