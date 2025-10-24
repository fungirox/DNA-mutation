import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DnaChecker } from './dna-checker';

describe('DnaChecker', () => {
  let component: DnaChecker;
  let fixture: ComponentFixture<DnaChecker>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DnaChecker]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DnaChecker);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
