
// Class for image resizing/compression utilities
export class ImageResizer {
  maxWidth: number;
  maxHeight: number;
  quality: number;
  mimeType: string;

  constructor(options?: { maxWidth?: number; maxHeight?: number; quality?: number; mimeType?: string }) {
    this.maxWidth = options?.maxWidth ?? 800;
    this.maxHeight = options?.maxHeight ?? 800;
    this.quality = options?.quality ?? 0.8;
    this.mimeType = options?.mimeType ?? 'image/jpeg';
  }

  resizeFileToBase64(file: File): Promise<string> {
    return new Promise((resolve, reject) => {
      const img = new window.Image();
      const reader = new FileReader();
      reader.onload = (e) => {
        img.src = e.target?.result as string;
      };
      img.onload = () => {
        let { width, height } = img;
        if (width > this.maxWidth || height > this.maxHeight) {
          const aspect = width / height;
          if (width > height) {
            width = this.maxWidth;
            height = Math.round(this.maxWidth / aspect);
          } else {
            height = this.maxHeight;
            width = Math.round(this.maxHeight * aspect);
          }
        }
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        if (!ctx) return reject('Canvas not supported');
        ctx.drawImage(img, 0, 0, width, height);
        const dataUrl = canvas.toDataURL(this.mimeType, this.quality);
        resolve(dataUrl);
      };
      img.onerror = reject;
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
  }

  // Static convenience method for one-off usage
  static resizeImageFileToBase64(
    file: File,
    maxWidth: number = 800,
    maxHeight: number = 800,
    quality: number = 0.8,
    mimeType: string = 'image/jpeg'
  ): Promise<string> {
    const resizer = new ImageResizer({ maxWidth, maxHeight, quality, mimeType });
    return resizer.resizeFileToBase64(file);
  }
}
