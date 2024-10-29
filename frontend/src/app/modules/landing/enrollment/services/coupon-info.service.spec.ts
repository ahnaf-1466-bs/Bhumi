import { TestBed } from '@angular/core/testing';

import { CouponInfoService } from './coupon-info.service';

describe('CouponInfoService', () => {
  let service: CouponInfoService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(CouponInfoService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
