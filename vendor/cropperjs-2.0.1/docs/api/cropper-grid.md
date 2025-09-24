# CropperGrid

The `CropperGrid` interface provides properties and methods for manipulating the layout and presentation of `<cropper-grid>` elements.

## Examples

### Basic

:::live-demo

```html
<cropper-grid></cropper-grid>
```

:::

:::tip
The default height of this element is `0`.
:::

### Custom rows and columns

:::live-demo

```html
<cropper-grid rows="4" columns="18" theme-color="#39f" style="height: 10rem;"></cropper-grid>
```

:::

### Within CropperCanvas

:::live-demo

```html
<cropper-canvas style="background-color: #39f;">
  <cropper-grid bordered covered></cropper-grid>
</cropper-canvas>
```

:::

### Within CropperSelection

:::live-demo

```html
<cropper-selection width="160" height="90" style="background-color: #39f;">
  <cropper-grid bordered covered></cropper-grid>
</cropper-selection>
```

:::

## Properties

Inherits properties from its parent, [`CropperElement`](cropper-element.html), and implements the following properties:

| Name | Type | Default | Options | Description |
| --- | --- | --- | --- | --- |
| rows | `number` | `3` | - | Indicates the number of the rows. |
| columns | `number` | `3` | - | Indicates the number of the columns. |
| bordered | `boolean` | `false` | - | Indicates whether this element is bordered. |
| covered | `boolean` | `false` | - | Indicates whether this element covers its parent element. |
| slottable | `boolean` | `false` | - | Indicates whether this element is slottable. |
| themeColor | `string` | `"rgba(238, 238, 238, 0.5)"` | - | Indicates the color of the element. |

## Slots

There are no available slots in this element.

> You can enable the default slot by setting the `slottable` property to `true`:
>
> ```html
> <cropper-grid slottable></cropper-grid>
> ```
