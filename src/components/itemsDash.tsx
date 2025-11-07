import { useQuery } from '@tanstack/react-query'
import { convexQuery } from '@convex-dev/react-query'
import { api } from "../../convex/_generated/api";
import { CircleUser } from 'lucide-react';
import { Skeleton } from '@mui/material';
    



export default function ItemsDash() {
    const {data, isLoading, refetch} = useQuery(convexQuery(api.items.getItems, {}));
    return (
            <div className='mt-10'>
            <div className='flex justify-between items-center'>
                <h1>Item</h1>
                <h1>User</h1>
                <h1>Price</h1>
                <h1>Created At</h1>
            </div>
            <div>
                {isLoading && 
                <div className='grid gap-2'>
                    <Skeleton variant="rectangular" width={'100%'} height={50} className='mb-4 rounded-md'/>
                    <Skeleton variant="rectangular" width={'100%'} height={50} className='mb-4 rounded-md'/>
                    <Skeleton variant="rectangular" width={'100%'} height={50} className='mb-4 rounded-md'/>
                    <Skeleton variant="rectangular" width={'100%'} height={50} className='mb-4 rounded-md'/>
                </div>
                }

                {data?
                    data.map((items) => (
                        <div key={items._id} className='border-b border-gray-200 py-4 flex justify-between items-center'>
                            <h2 className='text-lg font-semibold'>{items.itemName}</h2>
                            <p>{items.userId}</p>
                            <p className='text-sm text-gray-500'>x{items.price}</p>
                            <p>{new Date(items.createdAt).toLocaleDateString()}</p>
                        </div>
                    ))
                : (
                    <p>No orders found</p>
                )}
            </div>
        </div>
    )
}