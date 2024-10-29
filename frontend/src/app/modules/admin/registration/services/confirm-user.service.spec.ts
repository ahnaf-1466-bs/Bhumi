import { TestBed } from '@angular/core/testing';

import { ConfirmUserService } from './confirm-user.service';

describe('ConfirmUserService', () => {
  let service: ConfirmUserService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(ConfirmUserService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
