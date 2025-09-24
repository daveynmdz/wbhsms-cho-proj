import { EVENT_TRANSFORM } from '@cropper/utils';
import CropperImage from '../src';

// A 1×1 pixel PNG image.
const URL_EXAMPLE_IMAGE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQYV2P4////fwAJ+wP9BUNFygAAAABJRU5ErkJggg==';

CropperImage.$define();

describe('CropperImage', () => {
  describe('properties', () => {
    describe('initial-center-size', () => {
      it('should be `"contain"` by default', () => {
        const element = new CropperImage();

        expect(element.initialCenterSize).toBe('contain');
      });

      it('should be `"cover"`', () => {
        const element = new CropperImage();

        element.setAttribute('initial-center-size', 'cover');
        expect(element.initialCenterSize).toBe('cover');
      });
    });

    describe('rotatable', () => {
      it('should be `false` by default', () => {
        const element = new CropperImage();

        expect(element.rotatable).toBe(false);
      });

      it('should be `true`', () => {
        const element = new CropperImage();

        element.setAttribute('rotatable', 'true');
        expect(element.rotatable).toBe(true);
      });
    });

    describe('scalable', () => {
      it('should be `false` by default', () => {
        const element = new CropperImage();

        expect(element.scalable).toBe(false);
      });

      it('should be `true`', () => {
        const element = new CropperImage();

        element.setAttribute('scalable', 'true');
        expect(element.scalable).toBe(true);
      });
    });

    describe('skewable', () => {
      it('should be `false` by default', () => {
        const element = new CropperImage();

        expect(element.skewable).toBe(false);
      });

      it('should be `true`', () => {
        const element = new CropperImage();

        element.setAttribute('skewable', 'true');
        expect(element.skewable).toBe(true);
      });
    });

    describe('translatable', () => {
      it('should be `false` by default', () => {
        const element = new CropperImage();

        expect(element.translatable).toBe(false);
      });

      it('should be `true`', () => {
        const element = new CropperImage();

        element.setAttribute('translatable', 'true');
        expect(element.translatable).toBe(true);
      });
    });

    describe('slottable', () => {
      it('should be `false` by default', () => {
        const element = new CropperImage();

        expect(element.slottable).toBe(false);
      });

      it('should be `true`', () => {
        const element = new CropperImage();

        element.setAttribute('slottable', '');
        expect(element.slottable).toBe(true);
      });
    });
  });

  describe('methods', () => {
    describe('$move', () => {
      it('should move the image', () => {
        const element = new CropperImage();

        element.translatable = true;
        element.$move(10, 10);

        const matrix = element.$getTransform();

        expect(matrix[4]).not.toBe(0);
        expect(matrix[5]).not.toBe(0);
      });

      it('should default to the first parameter for the second parameter', () => {
        const element = new CropperImage();

        element.translatable = true;
        element.$move(10);

        const matrix = element.$getTransform();

        expect(matrix[5]).toBe(matrix[4]);
      });
    });

    describe('$moveTo', () => {
      it('should move the image to a specific position', () => {
        const element = new CropperImage();

        element.translatable = true;
        element.$moveTo(10, 10);

        const matrix = element.$getTransform();

        expect(matrix[4]).not.toBe(0);
        expect(matrix[5]).not.toBe(0);
      });

      it('should default to the first parameter for the second parameter', () => {
        const element = new CropperImage();

        element.translatable = true;
        element.$moveTo(10);

        const matrix = element.$getTransform();

        expect(matrix[5]).toBe(matrix[4]);
      });
    });

    describe('$rotate', () => {
      it('should rotate the image', () => {
        const element = new CropperImage();

        element.rotatable = true;
        element.$rotate(45);

        const [a, b, c, d] = element.$getTransform();

        expect(a).not.toBe(1);
        expect(b).not.toBe(0);
        expect(c).not.toBe(0);
        expect(d).not.toBe(1);
      });

      it('should support string parameter', () => {
        const element = new CropperImage();

        element.rotatable = true;
        element.$rotate('45deg');

        const [a, b, c, d] = element.$getTransform();

        expect(a).not.toBe(1);
        expect(b).not.toBe(0);
        expect(c).not.toBe(0);
        expect(d).not.toBe(1);
      });
    });

    describe('$zoom', () => {
      it('should zoom in the image', () => {
        const element = new CropperImage();

        element.scalable = true;
        element.$zoom(0.1);

        const matrix = element.$getTransform();

        expect(matrix[0]).toBeGreaterThan(1);
        expect(matrix[3]).toBeGreaterThan(1);
      });

      it('should zoom out the image', () => {
        const element = new CropperImage();

        element.scalable = true;
        element.$zoom(-0.1);

        const matrix = element.$getTransform();

        expect(matrix[0]).toBeLessThan(1);
        expect(matrix[3]).toBeLessThan(1);
      });
    });

    describe('$scale', () => {
      it('should scale the image', () => {
        const element = new CropperImage();

        element.scalable = true;
        element.$scale(1.1, 1.2);

        const matrix = element.$getTransform();

        expect(matrix[0]).not.toBe(1);
        expect(matrix[3]).not.toBe(1);
      });

      it('should default to the first parameter for the second parameter', () => {
        const element = new CropperImage();

        element.scalable = true;
        element.$scale(1.1);

        const matrix = element.$getTransform();

        expect(matrix[2]).toBe(matrix[1]);
      });
    });

    describe('$skew', () => {
      it('should skew the image', () => {
        const element = new CropperImage();

        element.skewable = true;
        element.$skew(0.1, 0.2);

        const matrix = element.$getTransform();

        expect(matrix[1]).not.toBe(0);
        expect(matrix[2]).not.toBe(0);
      });

      it('should support string parameter', () => {
        const element = new CropperImage();

        element.skewable = true;
        element.$skew('15deg', '30deg');

        const matrix = element.$getTransform();

        expect(matrix[1]).not.toBe(0);
        expect(matrix[2]).not.toBe(0);
      });

      it('should default to 0 for the second parameter', () => {
        const element = new CropperImage();

        element.skewable = true;
        element.$skew(0.1);

        const matrix = element.$getTransform();

        expect(matrix[1]).toBe(0);
        expect(matrix[2]).not.toBe(0);
      });
    });

    describe('$translate', () => {
      it('should translate the image', () => {
        const element = new CropperImage();

        element.translatable = true;
        element.$translate(10, 5);

        const matrix = element.$getTransform();

        expect(matrix[4]).toBe(10);
        expect(matrix[5]).toBe(5);
      });

      it('should default to the first parameter for the second parameter', () => {
        const element = new CropperImage();

        element.translatable = true;
        element.$translate(10);

        const matrix = element.$getTransform();

        expect(matrix[5]).toBe(matrix[4]);
      });
    });

    describe('$transform', () => {
      it('should transform the image', () => {
        const element = new CropperImage();

        element.rotatable = true;
        element.scalable = true;
        element.skewable = true;
        element.translatable = true;
        element.$transform(0.5, 0.5, 0.5, 0.5, 5, 5);
        expect(element.$getTransform()).toEqual([0.5, 0.5, 0.5, 0.5, 5, 5]);
      });
    });

    describe('$setTransform', () => {
      it('should reset (override) the current transform to the specific identity matrix', () => {
        const element = new CropperImage();

        element.rotatable = true;
        element.scalable = true;
        element.skewable = true;
        element.translatable = true;
        element.$setTransform(0.5, 0.5, 0.5, 0.5, 5, 5);
        expect(element.$getTransform()).toEqual([0.5, 0.5, 0.5, 0.5, 5, 5]);
      });

      it('should support array parameter', () => {
        const element = new CropperImage();

        element.rotatable = true;
        element.scalable = true;
        element.skewable = true;
        element.translatable = true;
        element.$setTransform([0.5, 0.5, 0.5, 0.5, 5, 5]);
        expect(element.$getTransform()).toEqual([0.5, 0.5, 0.5, 0.5, 5, 5]);
      });
    });

    describe('$getTransform', () => {
      it('should retrieves the current transformation matrix being applied to the element', () => {
        const element = new CropperImage();

        expect(element.$getTransform()).toEqual([1, 0, 0, 1, 0, 0]);
      });
    });

    describe('$resetTransform', () => {
      it('should reset the current transform to the initial identity matrix', () => {
        const element = new CropperImage();

        element.rotatable = true;
        element.scalable = true;
        element.skewable = true;
        element.translatable = true;
        expect(element.$getTransform()).toEqual([1, 0, 0, 1, 0, 0]);
        element.$setTransform([0.5, 0.5, 0.5, 0.5, 5, 5]);
        expect(element.$getTransform()).toEqual([0.5, 0.5, 0.5, 0.5, 5, 5]);
        element.$resetTransform();
        expect(element.$getTransform()).toEqual([1, 0, 0, 1, 0, 0]);
      });
    });
  });

  describe('events', () => {
    describe(EVENT_TRANSFORM, () => {
      it('should trigger the `transform` event', (done) => {
        const element = new CropperImage();

        element.rotatable = true;
        element.scalable = true;
        element.skewable = true;
        element.translatable = true;
        element.addEventListener(EVENT_TRANSFORM, (event) => {
          const { detail } = event as CustomEvent;

          expect(detail.matrix).toHaveLength(6);
          expect(detail.matrix).toBeInstanceOf(Array);
          expect(detail.oldMatrix).toHaveLength(6);
          expect(detail.oldMatrix).toBeInstanceOf(Array);
          done();
        });
        element.$setTransform(0.5, 0.5, 0.5, 0.5, 5, 5);
      });

      it('should not transform when default prevented', () => {
        const element = new CropperImage();

        element.rotatable = true;
        element.scalable = true;
        element.skewable = true;
        element.translatable = true;
        element.addEventListener(EVENT_TRANSFORM, (event) => {
          event.preventDefault();
        });
        expect(element.$getTransform()).toEqual([1, 0, 0, 1, 0, 0]);
        element.$setTransform(0.5, 0.5, 0.5, 0.5, 5, 5);
        expect(element.$getTransform()).toEqual([1, 0, 0, 1, 0, 0]);
      });
    });
  });

  describe('others', () => {
    it('should inherit the native attributes', () => {
      const element = new CropperImage();
      const img = element.querySelector('img');

      element.setAttribute('src', URL_EXAMPLE_IMAGE);
      element.setAttribute('alt', 'A 1×1 pixel PNG image');
      element.setAttribute('crossorigin', '');

      if (img) {
        expect(img.hasAttribute('src')).toBe(true);
        expect(img.hasAttribute('alt')).toBe(true);
        expect(img.hasAttribute('crossorigin')).toBe(true);
      }
    });
  });
});
