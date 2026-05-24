# Placeholder Assets for Testing

For immediate testing without downloading external assets, you can create simple placeholder images:

## Quick Placeholder Creation

You can create simple placeholder images using any image editor (Paint, GIMP, Photoshop) or online tools:

### Tiles (32x32 pixels)
- **lpc_base.png:** Create a 32x32 image with:
  - Tile 1: Dark gray (walls) - RGB(100, 100, 100)
  - Tile 2: Light brown (ground) - RGB(200, 180, 140)
  
### Sprites (32x32 pixels)
- **player.png:** Blue square - RGB(0, 100, 255)
- **guard.png:** Red square - RGB(255, 100, 100)
- **bonifacio.png:** Green square - RGB(100, 200, 100)
- **merchant.png:** Yellow square - RGB(255, 255, 100)

## Online Tool for Quick Placeholders

Visit: https://via.placeholder.com/
- For tiles: https://via.placeholder.com/32x32/8B4513/FFFFFF?text=Tile
- For sprites: https://via.placeholder.com/32x32/0000FF/FFFFFF?text=P

## For Development

You can also use colored rectangles directly in Phaser without image files by using:
```javascript
this.add.rectangle(x, y, 32, 32, 0x8B4513); // Brown tile
this.add.rectangle(x, y, 32, 32, 0x0000FF); // Blue player
```

This allows immediate testing while proper assets are being prepared.
