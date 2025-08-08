<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Document;
use App\Events\ModelTransitioning;
use App\Events\ModelTransitioned;
use Illuminate\Support\Facades\Event;

class StateMachineTest extends TestCase
{
    public function testDocumentTransitionToSubmitted()
    {
        // Assign: Create a document in the 'draft' state
        $document = Document::create([
            'title' => 'test document',
            'state' => 'draft'
        ]);

        // Action: Transition the document to 'submitted'
        $document->transitionTo('submitted');

        // Get the updated document from the database
        $documentData = Document::find($document->id);
        
        // Assert: document state should still not 'draft'
        $this->assertNotEquals('draft', $document->state);

        // Assert: document state should changed to 'submitted'
        $this->assertEquals('submitted', $document->state);
        $this->assertEquals($documentData->state, $document->state);
    }

    public function testDocumentTransitionToApproved()
    {
        // Assign: Create a document in the 'submitted' state
        $document = Document::create([
            'title' => 'test document',
            'state' => 'submitted'
        ]);

        // Action: Transition the document to 'approved'
        $document->transitionTo('approved');

        // Get the updated document from the database
        $documentData = Document::find($document->id);
        
        // Assert: document state should not be 'submitted'
        $this->assertNotEquals('submitted', $document->state);

        // Assert: document state should changed to 'approved'
        $this->assertEquals('approved', $document->state);
        $this->assertEquals($documentData->state, $document->state);
    }

    public function testInvalidTransitionThrowsException()
    {
        // Assign: Create a document in the 'draft' state
        $document = Document::create([
            'title' => 'test document',
            'state' => 'draft'
        ]);

        // Action & Assert: Attempt to transition to an invalid state
        $this->expectException(\InvalidArgumentException::class);
        $document->transitionTo('approved'); // Invalid transition from 'draft'
    }

    public function testTransitioningEventsDispatched()
    {
        // Assign: Create a document in the 'draft' state
        $document = Document::create([
            'title' => 'test document',
            'state' => 'draft'
        ]);

        // Assign: Listen for the transitioning event
        Event::fake();

        // Action: Transition the document to 'submitted'
        $document->transitionTo('submitted');

        // Assert: that the ModelTransitioning event was dispatched
        Event::assertDispatched(ModelTransitioning::class, function ($event) use ($document) {
            return $event->model->is($document) && $event->from === 'draft' && $event->to === 'submitted';
        });

        // Assert: that the ModelTransitioned event was dispatched
        Event::assertDispatched(ModelTransitioned::class, function ($event) use ($document) {
            return $event->model->is($document) && $event->from === 'draft' && $event->to === 'submitted';
        });
    }

    public function testTransitionToSameStateDoesNotDispatchEvents()
    {
        // Assign: Create a document in the 'draft' state
        $document = Document::create([
            'title' => 'test document',
            'state' => 'draft'
        ]);

        // Assign: Listen for the transitioning event
        Event::fake();

        // Action & Assert: Transition the document to the same state
        $this->expectException(\InvalidArgumentException::class);
        $document->transitionTo('draft');
    }
}
