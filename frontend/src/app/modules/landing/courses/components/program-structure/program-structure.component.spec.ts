import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ProgramSingleComponent } from '../program-single/program-single.component';

import { ProgramStructureComponent } from './program-structure.component';

describe('ProgramStructureComponent', () => {
  let component: ProgramStructureComponent;
  let fixture: ComponentFixture<ProgramStructureComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ProgramStructureComponent, ProgramSingleComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ProgramStructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
