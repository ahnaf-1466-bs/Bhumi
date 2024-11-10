import { TestBed } from '@angular/core/testing';

import { ProfileCompletionGuard } from './profile-completion.guard';

describe('ProfileCompletionGuard', () => {
  let guard: ProfileCompletionGuard;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    guard = TestBed.inject(ProfileCompletionGuard);
  });

  it('should be created', () => {
    expect(guard).toBeTruthy();
  });
});
